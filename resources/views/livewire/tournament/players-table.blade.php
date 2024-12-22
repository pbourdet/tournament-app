<div class="shadow-md rounded-lg mt-5">
    <flux:table>
        <flux:columns class="bg-gray-100">
            <flux:column/>
            <flux:column>{{ __('Player') }}</flux:column>
            <flux:column/>
            @can('manage', $tournament)
                <flux:column>{{ __('Actions') }}</flux:column>
            @endcan
        </flux:columns>

        <flux:rows>
            @foreach($players as $player)
                <flux:row wire:key="{{ $player->id }}">
                    <flux:cell class="w-12">
                        <flux:avatar class="ml-3" size="sm" src="{{ Storage::url($player->getProfilePicture()) }}"/>
                    </flux:cell>
                    <flux:cell>
                        @if($player->is(Auth::user()))
                            <span class="text-sm font-medium text-accent-content">{{ __('You') }}</span>
                        @else
                            <span class="text-sm font-medium">{{ $player->username }}</span>
                        @endif
                    </flux:cell>
                    <flux:cell>
                        @if($tournament->organizer->is($player))
                            <span class="text-amber-500">
                            <flux:icon.crown/>
                        </span>
                        @endif
                    </flux:cell>
                    @can('manage', $tournament)
                        <flux:cell>
                            <flux:button size="sm" variant="danger" icon="trash" :disabled="$locked" wire:click="remove('{{ $player->id }}')"/>
                        </flux:cell>
                    @endcan
                </flux:row>
            @endforeach
        </flux:rows>
    </flux:table>
</div>
