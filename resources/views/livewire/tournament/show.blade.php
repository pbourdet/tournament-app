<x-section>
    <x-tab-container :default-tab="'general'">
        <x-slot:tabs>
            <x-tab :tab-name="'general'"/>
            @if($tournament->team_based)
                <x-tab :tab-name="'teams'"/>
            @endif
        </x-slot:tabs>
        <x-slot:tabContents>
            <x-tab-content :tab-name="'general'">
                <div class="text-4xl capitalize">
                    {{ $tournament->name }}
                </div>
                <div>
                    {{ $tournament->players->count() }} / {{ $tournament->number_of_players }}
                </div>

            </x-tab-content>
            @if($tournament->team_based)
                <x-tab-content :tab-name="'teams'">
                    <livewire:tournament.teams :tournament="$tournament"/>
                </x-tab-content>
            @endif
        </x-slot:tabContents>
    </x-tab-container>
</x-section>
