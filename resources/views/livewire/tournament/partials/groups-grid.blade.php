@if(null === $groupPhase)
    <flux:subheading>
        {{ __('Group phase has not been set up yet.') }}
    </flux:subheading>
@else
    <div>
        <div class="grid max-sm:grid-cols-1 max-2xl:grid-cols-2 grid-cols-3 gap-1">
            @foreach($groupPhase->groups as $group)
                <livewire:tournament.group-card :$organizerMode
                    :key="$group->id . '-' . '-' . count($this->selectableContestants)"
                    :selectableContestants="$this->selectableContestants" :$tournament :$group/>
            @endforeach
        </div>
    </div>
@endif
