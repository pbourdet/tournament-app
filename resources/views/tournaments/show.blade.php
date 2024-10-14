<x-app-layout>
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
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1">
                            @foreach($tournament->teams as $team)
                                <div class="max-w-sm rounded-lg overflow-hidden shadow-lg bg-white mb-2">
                                    <div class="px-6 py-4">
                                        <div class="font-bold text-xl mb-2">{{ $team->name }}</div>
                                        <p class="text-gray-700 text-base">
                                            @foreach($team->members as $member)
                                                <span class="block">
                                                {{ $member->name }}
                                            </span>
                                            @endforeach
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-tab-content>
                @endif
            </x-slot:tabContents>
        </x-tab-container>
    </x-section>
</x-app-layout>
