<x-slot name="title">{{ __($title) }} - {{ __('Organizer mode') }} - {{ $tournament->name }}</x-slot>

<div>
    <flux:header class="bg-zinc-50">
        <flux:navbar scrollable style="--color-accent: var(--color-orange-500); --color-accent-content: var(--color-orange-600);">
            @can('manage', $tournament)
                <flux:dropdown>
                    <flux:navbar.item icon-trailing="chevron-down">{{ __('Organizer mode') }}</flux:navbar.item>

                    <flux:navmenu>
                        <flux:navmenu.item wire:navigate
                                           href="{{ route('tournaments.show', ['tournament' => $tournament, 'page' => 'overview']) }}">
                            {{ __('Player mode') }}
                        </flux:navmenu.item>
                    </flux:navmenu>
                </flux:dropdown>
            @endcan
            <flux:navbar.item wire:navigate dusk="link-organize-general"
                              href="{{ route('tournaments.organize', ['tournament' => $tournament, 'page' => 'general']) }}">
                {{ __('General') }}
            </flux:navbar.item>
            <flux:navbar.item wire:navigate dusk="link-organize-players"
                              href="{{ route('tournaments.organize', ['tournament' => $tournament, 'page' => 'players']) }}">
                {{ __('Players') }}
            </flux:navbar.item>
            <flux:navbar.item wire:navigate dusk="link-organize-teams"
                              href="{{ route('tournaments.organize', ['tournament' => $tournament, 'page' => 'teams']) }}">
                {{ __('Teams') }}
            </flux:navbar.item>
            <flux:dropdown>
                <flux:navbar.item dusk="qualification-dropdown" icon-trailing="chevron-down">{{ __('Qualification') }}</flux:navbar.item>

                <flux:navmenu>
                    <flux:navmenu.item wire:navigate dusk="link-organize-groups"
                                       href="{{ route('tournaments.organize', ['tournament' => $tournament, 'page' => 'groups']) }}">
                        {{ __('Groups') }}
                    </flux:navmenu.item>
                </flux:navmenu>
            </flux:dropdown>
            <flux:navbar.item wire:navigate dusk="link-organize-elimination"
                              wire:navigate href="{{ route('tournaments.organize', ['tournament' => $tournament, 'page' => 'elimination']) }}">
                {{ __('Elimination') }}
            </flux:navbar.item>
        </flux:navbar>
    </flux:header>

    <flux:main>
        <livewire:is :component="sprintf('tournament.organize.%s', $page)" :$tournament />
    </flux:main>

    @script
    <script>
        window.Echo.private('App.Models.Tournament.{{ $tournament->id }}')
            .listen('TournamentUpdated', function() {
                let parentComponent = Livewire.getByName('tournament.organizer-zone')[0];
                if (parentComponent === undefined) return;
                let childComponent = Livewire.getByName('tournament.organize.'+ parentComponent.page)[0];
                childComponent.$refresh();
            });
    </script>
    @endscript
</div>
