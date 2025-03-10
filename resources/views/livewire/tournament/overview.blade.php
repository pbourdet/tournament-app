<div class="space-y-6">
    <div class="max-sm:flex-col flex justify-between">
        <div>
            <flux:heading size="xl" level="1">
                {{ $tournament->name }} <flux:badge inset="top bottom" color="lime">{{ __($tournament->status->value) }}</flux:badge>
            </flux:heading>
            <flux:subheading>{{ __('Organized by') }} {{ $tournament->organizer->username }}</flux:subheading>
        </div>
        @can('manage', $tournament)
            <div class="max-sm:hidden">
                <livewire:tournament.invitation :$tournament/>
            </div>
        @endcan
    </div>
    <div>
        {{ $tournament->players->count() }} / {{ $tournament->number_of_players }}
    </div>
    <div>
        {{ $tournament->invitation?->code }}
    </div>

    <livewire:tournament.players-table :$organizerMode :$tournament :$locked/>
</div>
