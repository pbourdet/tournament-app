<?php

declare(strict_types=1);

namespace App\Livewire\Tournament\Organize;

use App\Events\PhaseCreated;
use App\Livewire\Component;
use App\Livewire\Forms\Tournament\Phase\CreateGroupsForm;
use App\Models\Contestant;
use App\Models\Group;
use App\Models\Team;
use App\Models\Tournament;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

class Groups extends Component
{
    public Tournament $tournament;

    #[Url]
    public string $tab = 'settings';

    #[Locked]
    public bool $organizerMode = true;

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

    /** @return Collection<int, Team>|Collection<int, User> */
    #[Computed]
    public function selectableContestants(): Collection
    {
        $contestantsWithGroup = $this->tournament->groupPhase?->groups->flatMap(fn (Group $group) => $group->getContestants()->map->id);

        if (null === $contestantsWithGroup) return Collection::empty();

        return $this->tournament->contestants()->reject(fn (Contestant $contestant) => $contestantsWithGroup->contains($contestant->id));
    }

    public function create(): void
    {
        $this->authorize('manage', $this->tournament);
        $this->form->validate();
        $this->tournament->groupPhase?->delete();

        $this->tournament->groupPhase()->create([
            'number_of_groups' => $this->form->numberOfGroups,
            'qualifying_per_group' => $this->form->contestantsQualifying,
        ]);

        PhaseCreated::dispatch($this->tournament->refresh());
        $this->toastSuccess(__('Phase created !'));
    }

    public function addContestant(Group $group, string $contestantId): void
    {
        $this->authorize('manage', $this->tournament);

        $contestant = $this->tournament->contestants()->firstOrFail(fn (Contestant $contestant) => $contestant->id === $contestantId);

        if ($this->selectableContestants()->doesntContain($contestant)) abort(403);
        if ($group->contestants->count() >= $group->size) abort(403);

        $group->addContestants([$contestant]);
        $this->toastSuccess(__(':contestant added to group !', ['contestant' => ucfirst($this->tournament->getContestantsTranslation())]));
    }

    public function removeContestant(Group $group, string $contestantId): void
    {
        $this->authorize('manage', $this->tournament);

        $contestant = $this->tournament->contestants()->firstOrFail(fn (Contestant $contestant) => $contestant->id === $contestantId);

        $group->contestants()->where('contestant_id', $contestant->id)->delete();
        $this->toastSuccess(__(':contestant removed from group !', ['contestant' => ucfirst($this->tournament->getContestantsTranslation())]));
    }
}
