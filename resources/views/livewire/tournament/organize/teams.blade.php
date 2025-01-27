<div>
    @if(!$tournament->team_based)
        <div class="space-y-6">
            <flux:heading>
                {{ __('Your tournament is not played in teams. You can change this setting in the "General" tab.') }}
            </flux:heading>
            <div>
                <flux:link class="text-sm" wire:navigate href="{{ route('tournaments.organize', ['tournament' => $tournament, 'page' => 'general']) }}">
                    {{ __('Go to general settings') }}
                </flux:link>
            </div>
        </div>
    @else
        Hello!
    @endif
</div>
