<div class="max-w-sm rounded-lg shadow-md border border-zinc-200 dark:border-zinc-900 mb-4">
    <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-900 bg-zinc-50 dark:bg-zinc-700 flex items-center justify-between">
        <flux:heading size="lg" class="bg-zinc-50 dark:bg-zinc-700">
            {{ $group->name  }}
        </flux:heading>
    </div>
    <div>
        @if($group->contestants->isEmpty())
            <flux:subheading class="p-4 text-center">
                {{ __('No :contestants in this group.', ['contestants' => $tournament->getContestantsTranslation(true)]) }}
            </flux:subheading>
        @else
            <div class="mx-2">
                <flux:table class="w-full">
                    <flux:columns>
                        <flux:column class="w-1/2">{{ __('Contestants') }}</flux:column>
                        <flux:column>{{ __('Win') }}</flux:column>
                        <flux:column>{{ __('Loss') }}</flux:column>
                        <flux:column>{{ __('Tie') }}</flux:column>
                    </flux:columns>

                    <flux:rows>
                        @foreach($group->getContestants() as $contestant)
                            <flux:row>
                                <flux:cell class="truncate">
                                    {{ $contestant->getName() }}
                                </flux:cell>
                                <flux:cell>
                                    {{ 0 }}
                                </flux:cell>
                                <flux:cell>
                                    {{ 0 }}
                                </flux:cell>
                                <flux:cell>
                                    {{ 0 }}
                                </flux:cell>
                            </flux:row>
                        @endforeach
                    </flux:rows>
                </flux:table>
            </div>
        @endif
        @if($group->contestants->count() < $group->size)
            <div class="p-2" x-data="{ loading: false }">
                <flux:icon.loading x-cloak class="m-auto" x-show="loading"/>
                <flux:select variant="listbox" searchable size="sm"
                             :placeholder="__('Add :contestant', ['contestant' => $tournament->getContestantsTranslation()])"
                             dusk="select-contestants"
                             x-show="!loading">
                    <x-slot name="search">
                        <flux:select.search placeholder="{{ __('Search :contestant', ['contestant' => $this->tournament->getContestantsTranslation()]) }}"/>
                    </x-slot>
                    @foreach($selectableContestants as $contestant)
                        <flux:option @click="loading = true"
                                     wire:click="$parent.addContestant('{{ $group->id }}', '{{ $contestant->id }}')"
                                     dusk="select-contestant-{{ $loop->index }}" :value="$contestant->id">
                            {{ $contestant->getName() }}
                        </flux:option>
                    @endforeach
                </flux:select>
            </div>
        @endif
    </div>
</div>
