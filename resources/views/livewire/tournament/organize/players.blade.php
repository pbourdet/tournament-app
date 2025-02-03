<div>
    <flux:badge variant="pill" color="teal" icon="user">{{ $tournament->players->count() }} / {{ $tournament->number_of_players }}</flux:badge>
    <livewire:tournament.players-table :$tournament :$locked/>
</div>
