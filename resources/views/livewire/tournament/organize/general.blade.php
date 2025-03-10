<div>
    @if($tournament->isNotStarted())
        <flux:card class="space-y-4 shadow lg:max-w-2/3">
            <flux:heading>
                {{ __('To start the tournament') }}
            </flux:heading>
            <ul class="text-sm space-y-2">
                @include('livewire.tournament.partials.start-condition', [
                    'condition' => $tournament->isFull(),
                    'text' => __('All the players must have joined the tournament')
                ])
                @include('livewire.tournament.partials.start-condition', [
                    'condition' => $tournament->getPhases()->isNotEmpty(),
                    'text' => __('The tournament must have at least one phase (elimination or qualification)')
                ])
                @if(null !== $tournament->groupPhase)
                    @include('livewire.tournament.partials.start-condition', [
                        'condition' => $tournament->groupPhase->isReadyToStart(),
                        'text' => __('The groups must have all their :contestants', ['contestants' => $tournament->getContestantsTranslation(true)])
                    ])
                @endif
                @if($tournament->team_based)
                    @include('livewire.tournament.partials.start-condition', [
                        'condition' => $tournament->teams->every->isFull(),
                        'text' => __('Every player must be in a team')
                    ])
                @endif
            </ul>
            <flux:button dusk="start-tournament" :disabled="$this->user->cannot('start', $tournament)" wire:click="start">
                {{ __('Start tournament') }}
            </flux:button>
        </flux:card>
    @endif
</div>
