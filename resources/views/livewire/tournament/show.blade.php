<x-slot name="title">{{ __($title) }} - {{ $tournament->name }}</x-slot>

<div>
    <flux:header class="bg-zinc-50">
        <flux:navbar scrollable>
            <flux:navbar.item wire:navigate dusk="link-overview"
                              href="{{ route('tournaments.show', ['tournament' => $tournament, 'page' => 'overview']) }}">
                {{ __('Overview') }}
            </flux:navbar.item>
            @if($tournament->team_based)
                <flux:navbar.item wire:navigate dusk="link-teams"
                                  href="{{ route('tournaments.show', ['tournament' => $tournament, 'page' => 'teams']) }}">
                    {{ __('Teams') }}
                </flux:navbar.item>
            @endif
            <flux:navbar.item wire:navigate dusk="link-qualification"
                              href="{{ route('tournaments.show', ['tournament' => $tournament, 'page' => 'phase-qualification']) }}">
                {{ __('Qualification') }}
            </flux:navbar.item>
            <flux:navbar.item wire:navigate dusk="link-elimination"
                              href="{{ route('tournaments.show', ['tournament' => $tournament, 'page' => 'phase-elimination']) }}">
                {{ __('Elimination') }}
            </flux:navbar.item>
        </flux:navbar>
    </flux:header>

    <flux:main>
        <livewire:is :component="$page" :$tournament/>
    </flux:main>

    @script
    <script>
        window.Echo.private('App.Models.Tournament.{{ $tournament->id }}')
            .listen('TournamentUpdated', function() {
                let parentComponent = Livewire.getByName('tournament.show')[0];
                if (parentComponent === undefined) return;
                let childComponent = Livewire.getByName(parentComponent.page)[0];
                childComponent.$refresh();
            });
    </script>
    @endscript
</div>
