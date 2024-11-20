<div>
    @if($tournament->organizer->is(auth()->user()) && $tournament->canGenerateTeams())
        <div>
            <x-primary-button type="button" wire:click="generate" id="button-generate-teams">
                {{ __('Generate teams') }}
            </x-primary-button>
        </div>
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1">
        @foreach($tournament->teams as $team)
            <div class="max-w-sm rounded-lg overflow-hidden shadow-lg bg-white mb-2">
                <div class="px-6 py-4">
                    <div class="font-bold text-xl mb-2">{{ $team->name }}</div>
                    <p class="text-gray-700 text-base">
                        @foreach($team->members as $member)
                            <span class="block">{{ $member->name }}</span>
                        @endforeach
                    </p>
                </div>
            </div>
        @endforeach
    </div>
</div>
