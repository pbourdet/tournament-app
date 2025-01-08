<div>
    @if(null === $eliminationPhase)
        {{ __('No elimination phase for this tournament') }}
        @can('manage', $tournament)
            <form wire:submit="create">
                <x-input-label for="numberOfContestants" :value="$tournament->team_based ? __('Number of teams') : __('Number of players')" />
                <select dusk="number-of-contestants" id="numberOfContestants" wire:model="form.numberOfContestants" class="block mt-1 w-20">
                    @foreach([2, 4, 8, 16, 32, 64, 128] as $number)
                        @if($number <= $tournament->contestantsCount())
                            <option value="{{ $number }}">{{ $number }}</option>
                        @endif
                    @endforeach
                </select>

                <x-secondary-button dusk="create-elimination" type="submit">
                    {{ __('Confirm') }}
                </x-secondary-button>
            </form>
        @endcan
    @else
        <div class="flex flex-col">
            <div class="flex space-x-8 overflow-x-auto">
                @foreach ($eliminationPhase->rounds as $round)
                    <div class="flex flex-col space-y-6 justify-center">
                        <h2 class="text-lg font-semibold text-center">{{ $round->stage->value }}</h2>
                        @foreach ($round->matches as $match)
                            <livewire:tournament.match-card :$match :key="$match->id . '-' . $match->contestants->count()">
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
