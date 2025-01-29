<div>
    <flux:badge icon-trailing="users" variant="pill">
        <span class="font-bold">{{ $tournament->teams()->count() }}/{{$tournament->maxTeamsCount()}}</span>
    </flux:badge>
    @include('livewire.tournament.partials.teams-grid')
</div>
