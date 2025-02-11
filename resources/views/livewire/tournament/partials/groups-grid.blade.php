@if(null === $tournament->groupPhase)
    <flux:subheading>
        {{ __('Group phase has not been set up yet.') }}
    </flux:subheading>
@else
    <div>
        <div class="grid max-sm:grid-cols-1 grid-cols-2 gap-1">
            @foreach($tournament->groupPhase->groups as $group)
                <livewire:tournament.group-card
                    :key="$group->id . '-' . '-' . count($this->selectableContestants)"
                    :selectableContestants="$this->selectableContestants" :$tournament :$group/>
            @endforeach
        </div>
    </div>
@endif
