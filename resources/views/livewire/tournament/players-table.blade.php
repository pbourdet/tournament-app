<div class="shadow-md rounded-lg mt-5">
    <flux:table>
        <flux:table.columns class="bg-zinc-100 dark:bg-zinc-700">
            <flux:table.column/>
            <flux:table.column>{{ __('Player') }}</flux:table.column>
            <flux:table.column/>
            @can('manage', $tournament)
                <flux:table.column>{{ __('Actions') }}</flux:table.column>
            @endcan
        </flux:table.columns>

        <flux:table.rows>
            @foreach($players as $player)
                <flux:table.row wire:key="{{ $player->id }}">
                    <flux:table.cell class="w-12">
                        <flux:avatar class="ml-3" size="sm" src="{{ Storage::url($player->getProfilePicture()) }}"/>
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($player->is($this->user))
                            <span class="text-sm font-medium text-accent-content">{{ __('You') }}</span>
                        @else
                            <span class="text-sm font-medium">{{ $player->username }}</span>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell>
                        @if($tournament->organizer->is($player))
                            <span class="text-amber-500">
                            <flux:icon.crown/>
                        </span>
                        @endif
                    </flux:table.cell>
                    @can('manage', $tournament)
                        <flux:table.cell>
                            <flux:button dusk="remove-player-{{ $loop->index }}" size="sm" variant="danger" icon="trash" :disabled="$locked" wire:click="remove('{{ $player->id }}')"/>
                        </flux:table.cell>
                    @endcan
                </flux:table.row>
            @endforeach
        </flux:table.rows>
    </flux:table>
</div>
