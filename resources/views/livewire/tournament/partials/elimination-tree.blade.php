@if(null === $eliminationPhase)
    <flux:subheading>
        {{ __('No elimination phase has yet been set up for this tournament.') }}
    </flux:subheading>
@elseif($eliminationPhase->rounds->isEmpty())
    <flux:subheading>
        {{ __('The matches will be displayed once the elimination phase has started.') }}
    </flux:subheading>
@else
    <div class="flex flex-col">
        <div class="flex space-x-8 overflow-x-auto">
            @foreach ($eliminationPhase->rounds as $round)
                <div class="flex flex-col space-y-6 justify-center">
                    <h2 class="text-lg font-semibold text-center">{{ $round->stage }}</h2>
                    @foreach ($round->matches as $match)
                        <livewire:tournament.match-card :$match :key="$match->id . '-' . $match->contestants->pluck('contestant_id')->join('-')">
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
@endif
