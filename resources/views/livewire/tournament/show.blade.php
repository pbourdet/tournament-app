<x-section>
    <x-tab-container :default-tab="'general'">
        <x-slot:tabs>
            <x-tab :tab-name="'general'"/>
            @if($tournament->team_based)
                <x-tab :tab-name="'teams'"/>
            @endif
            <x-tab :tab-name="'elimination'"/>
        </x-slot:tabs>
        <x-slot:tabContents>
            <x-tab-content :tab-name="'general'">
                <div class="text-4xl capitalize">
                    {{ $tournament->name }}
                </div>
                <div>
                    {{ $tournament->players->count() }} / {{ $tournament->number_of_players }}
                </div>
                <div>
                    {{ __($tournament->status->value) }}
                </div>

                @if($tournament->isNotStarted())
                    <div>
                        <x-primary-button type="button" :disabled="!$tournament->isReadyToStart() || $generationInProgress"
                                          wire:click="start" wire:loading.attr="disabled" wire:target="start">
                            {{ __('Start tournament') }}
                        </x-primary-button>
                    </div>
                @endif

            </x-tab-content>
            @if($tournament->team_based)
                <x-tab-content :tab-name="'teams'">
                    <livewire:tournament.teams wire:model="tournament" :$tournament :$generationInProgress/>
                </x-tab-content>
            @endif

            <x-tab-content :tab-name="'elimination'">
                <livewire:tournament.phase.elimination wire:model="tournament" :$tournament/>
            </x-tab-content>
        </x-slot:tabContents>
    </x-tab-container>
    @script
    <script>
        window.Echo.private('App.Models.Tournament.{{ $tournament->id }}')
            .listen('TournamentUpdated', () => $wire.$refresh())
        ;
    </script>
    @endscript
</x-section>
