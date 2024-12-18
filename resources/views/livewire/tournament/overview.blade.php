<div>
    <flux:heading size="xl" level="1">{{ $tournament->name }}</flux:heading>
    <div>
        {{ $tournament->players->count() }} / {{ $tournament->number_of_players }}
    </div>
    <div>
        {{ __($tournament->status->value) }}
    </div>

    @if($tournament->isNotStarted())
        <div>
            <x-primary-button type="button" :disabled="!$tournament->isReadyToStart() || $locked" wire:click="start">
                {{ __('Start tournament') }}
            </x-primary-button>
        </div>
    @endif
</div>
