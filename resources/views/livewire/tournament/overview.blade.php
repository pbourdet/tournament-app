<div class="space-y-6">
    <div>
        <flux:heading size="xl" level="1">
            {{ $tournament->name }} <flux:badge inset="top bottom" color="lime">{{ __($tournament->status->value) }}</flux:badge>
        </flux:heading>
        <flux:subheading>{{ __('Organized by') }} {{ $tournament->organizer->username }}</flux:subheading>
    </div>
    <div>
        {{ $tournament->players->count() }} / {{ $tournament->number_of_players }}
    </div>
    <div>
        {{ $tournament->invitation?->code }}
    </div>

    @if($tournament->isNotStarted())
        <div>
            <flux:button variant="primary" :disabled="!$tournament->isReadyToStart() || $locked" wire:click="start">
                {{ __('Start tournament') }}
            </flux:button>
        </div>
    @endif

    <livewire:tournament.players-table :$tournament :$locked/>
</div>
