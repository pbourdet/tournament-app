<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Events\PhaseCreated;
use App\Events\TournamentUpdated;
use App\Jobs\GenerateGroups;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\Phase\CreateGroupsForm;
use App\Livewire\Tournament\WithDeletePhaseAction;
use App\Models\Contestant;
use App\Models\Group;
use App\Models\GroupPhase;
use App\Models\Tournament;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class Groups extends Component
{
    use WithDeletePhaseAction;

    public Tournament $tournament;

    #[Url]
    public string $tab = 'settings';

    public CreateGroupsForm $form;

    public function boot(): void
    {
        $this->tournament->load('groupPhase.groups.contestants');
        $this->form->setTournament($this->tournament);
    }

    public function render(): View
    {
        $this->tournament->load([
            'groupPhase.groups.contestants',
            'groupPhase.groups.phase',
            'groupPhase.rounds.matches.results',
            'groupPhase.rounds.matches.contestants',
        ]);

        return view('livewire.tournament.organize.groups');
    }

    /** @return Collection<int, covariant Contestant> */
    #[Computed]
    public function selectableContestants(): Collection
    {
        return $this->tournament->contestantsWithoutGroup();
    }

    public function create(): void
    {
        $this->authorize('create', [GroupPhase::class, $this->tournament]);
        $this->form->validate();
        $this->tournament->groupPhase?->delete();

        $this->tournament->groupPhase()->create([
            'number_of_groups' => $this->form->numberOfGroups,
            'qualifying_per_group' => $this->form->contestantsQualifying,
        ]);

        event(new PhaseCreated($this->tournament->refresh()));
        $this->tab = 'groups';
        $this->toastSuccess(__('Phase created !'));
    }

    public function addContestant(Group $group, string $contestantId): void
    {
        $contestant = $this->tournament->contestants()->firstOrFail(fn (Contestant $contestant) => $contestant->id === $contestantId);
        $this->authorize('addContestant', [$group, $contestant, $this->tournament]);

        $group->addContestant($contestant);
        event(new TournamentUpdated($this->tournament, broadcastToCurrentUser: false));
        $this->toastSuccess(__(':contestant added to group !', ['contestant' => mb_ucfirst($this->tournament->getContestantsTranslation())]));
    }

    public function removeContestant(Group $group, string $contestantId): void
    {
        $contestant = $this->tournament->contestants()->firstOrFail(fn (Contestant $contestant) => $contestant->id === $contestantId);
        $this->authorize('removeContestant', [$group, $contestant, $this->tournament]);

        $group->contestants()->where('contestant_id', $contestant->id)->delete();
        event(new TournamentUpdated($this->tournament, broadcastToCurrentUser: false));
        $this->toastSuccess(__(':contestant removed from group !', ['contestant' => mb_ucfirst($this->tournament->getContestantsTranslation())]));
    }

    public function generateGroups(): void
    {
        /** @var GroupPhase $groupPhase */
        $groupPhase = $this->tournament->groupPhase;

        $this->authorize('generateGroups', [$groupPhase, $this->tournament]);

        dispatch(new GenerateGroups($groupPhase));
        $this->toast(__('Groups generation in progress...'));
    }
}
