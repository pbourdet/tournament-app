@if(null === $tournament->groupPhase)
    <flux:subheading>
        {{ __('Group phase has not been set up yet.') }}
    </flux:subheading>
@else
    <div>
        <div class="grid grid-cols-4 gap-1">
            @foreach($tournament->groupPhase->groups as $group)
                <flux:card class="space-y-6">
                    <flux:heading size="lg">{{ $group->name  }}</flux:heading>
                    @if($group->contestants->isEmpty())
                        <flux:subheading>
                            {{ __('No :contestants in this group.', ['contestants' => $tournament->getContestantsTranslation(true)]) }}
                        </flux:subheading>
                    @else
                        <div class="flex flex-col">
                            @foreach($group->getContestants() as $contestant)
                                <div>
                                    {{ $contestant->getName() }}
                                </div>
                            @endforeach
                        </div>
                    @endif
                </flux:card>
            @endforeach
        </div>
    </div>
@endif
