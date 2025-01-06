<div>
    @if(null === $eliminationPhase)
        {{ __('No elimination phase for this tournament') }}
        @can('manage', $tournament)
            <form wire:submit="create">
                <x-input-label for="numberOfContestants" :value="$tournament->team_based ? __('Number of teams') : __('Number of players')" />
                <select id="numberOfContestants" name="numberOfContestants" wire:model="form.numberOfContestants" class="block mt-1 w-20">
                    @foreach([2, 4, 8, 16, 32, 64, 128] as $number)
                        @if($number <= $tournament->contestantsCount())
                            <option value="{{ $number }}">{{ $number }}</option>
                        @endif
                    @endforeach
                </select>

                <x-secondary-button type="submit">
                    {{ __('Confirm') }}
                </x-secondary-button>
            </form>
        @endcan
    @else
        <div class="flex flex-col items-center">
            <div class="flex space-x-8 overflow-x-auto">
                @foreach ($eliminationPhase->rounds as $round)
                    <div class="flex flex-col space-y-6">
                        <h2 class="text-lg font-semibold text-center">{{ $round->stage->value }}</h2>
                        @foreach ($round->matches as $match)
                            @include('livewire.tournament.partials.match', ['match' => $match])
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
