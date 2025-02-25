<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Events\PhaseCreated;
use App\Jobs\GenerateGroups;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\Phase\CreateGroupsForm;
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
        $this->tournament->load('groupPhase.groups.contestants');

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

        PhaseCreated::dispatch($this->tournament->refresh());
        $this->toastSuccess(__('Phase created !'));
        $this->tab = 'groups';
    }

    public function addContestant(Group $group, string $contestantId): void
    {
        $contestant = $this->tournament->contestants()->firstOrFail(fn (Contestant $contestant) => $contestant->id === $contestantId);
        $this->authorize('addContestant', [$group, $contestant, $this->tournament]);

        $group->addContestant($contestant);
        $this->toastSuccess(__(':contestant added to group !', ['contestant' => ucfirst($this->tournament->getContestantsTranslation())]));
    }

    public function removeContestant(Group $group, string $contestantId): void
    {
        $contestant = $this->tournament->contestants()->firstOrFail(fn (Contestant $contestant) => $contestant->id === $contestantId);
        $this->authorize('removeContestant', [$group, $contestant, $this->tournament]);

        $group->contestants()->where('contestant_id', $contestant->id)->delete();
        $this->toastSuccess(__(':contestant removed from group !', ['contestant' => ucfirst($this->tournament->getContestantsTranslation())]));
    }

    public function generateGroups(): void
    {
        /** @var GroupPhase $groupPhase */
        $groupPhase = $this->tournament->groupPhase;

        $this->authorize('generateGroups', [$groupPhase, $this->tournament]);

        GenerateGroups::dispatch($groupPhase);
        $this->toast(__('Groups generation in progress...'));
    }
}
