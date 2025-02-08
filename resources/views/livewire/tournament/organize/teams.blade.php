<div x-data="{generationStarted: false, canGenerate: @js($tournament->canGenerateTeams()), locked: @js($locked)}" class="space-y-6">
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
        <div class="flex justify-between items-center">
            <div class="flex flex-col gap-2 sm:flex-row">
                <div wire:replace class="flex items-center">
                    <flux:button x-on:click="generationStarted = true"
                                 x-bind:disabled="generationStarted || locked || !canGenerate"
                                 type="button" wire:click="generate" dusk="generate-teams">
                        <span>{{ __('Generate teams') }}</span>
                    </flux:button>
                    <flux:tooltip toggleable>
                        <flux:button size="sm" variant="ghost" icon="information-circle"/>

                        <flux:tooltip.content>
                            <p>{{ __('This will generate the remaining teams randomly.') }}</p>
                            <p>{{ __('Some other actions such as modifying teams will be prevented during this process') }}</p>
                        </flux:tooltip.content>
                    </flux:tooltip>
                </div>
            </div>
        </div>

        @include('livewire.tournament.partials.teams-grid')
    @endif
</div>
