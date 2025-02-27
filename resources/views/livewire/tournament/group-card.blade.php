<div class="rounded-lg shadow-md border border-zinc-200 dark:border-zinc-900 mb-4">
    <div class="px-4 py-3 border-b border-zinc-200 dark:border-zinc-900 bg-zinc-50 dark:bg-zinc-700 flex items-center justify-between">
        <flux:heading size="lg" class="bg-zinc-50 dark:bg-zinc-700">
            <span>{{ $group->name  }}</span>
            <span class="ml-2 text-xs">({{ $group->contestants->count() }}/{{ $group->size }})</span>
        </flux:heading>
    </div>
    <div class="dark:bg-zinc-900">
        @if($group->contestants->isEmpty())
            <flux:subheading class="p-4 text-center">
                {{ __('No :contestants in this group.', ['contestants' => $tournament->getContestantsTranslation(true)]) }}
            </flux:subheading>
        @else
            <div class="mx-2">
                <flux:table class="w-full">
                    <flux:table.columns>
                        <flux:table.column class="w-1/2">{{ ucfirst($tournament->getContestantsTranslation()) }}</flux:table.column>
                        <flux:table.column>{{ __('Win') }}</flux:table.column>
                        <flux:table.column>{{ __('Loss') }}</flux:table.column>
                        <flux:table.column>{{ __('Tie') }}</flux:table.column>
                        @if($organizerMode)
                            <flux:table.column/>
                        @endif
                    </flux:table.columns>

                    <flux:table.rows>
                        @foreach($group->getContestants() as $contestant)
                            <flux:table.row>
                                <flux:table.cell class="truncate">
                                    {{ $contestant->getName() }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $contestant->getMatchesForGroup($group, \App\Enums\ResultOutcome::WIN)->count() }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $contestant->getMatchesForGroup($group, \App\Enums\ResultOutcome::LOSS)->count() }}
                                </flux:table.cell>
                                <flux:table.cell>
                                    {{ $contestant->getMatchesForGroup($group, \App\Enums\ResultOutcome::TIE)->count() }}
                                </flux:table.cell>
                                @if($organizerMode)
                                    <flux:table.cell class="pt-[18px]" x-data="{loading: false}">
                                        <flux:icon.loading x-cloak x-show="loading"/>
                                        <flux:button size="sm" inset x-show="!loading" @click="loading = true" class="text-red-500!"
                                                     icon="x-circle" variant="subtle" dusk="remove-contestant-{{ $loop->index }}"
                                                     wire:click="$parent.removeContestant('{{ $group->id }}', '{{ $contestant->id }}')"/>
                                    </flux:table.cell>
                                @endif

                            </flux:table.row>
                        @endforeach
                    </flux:table.rows>
                </flux:table>
            </div>
        @endif
        @if($organizerMode && $group->contestants->count() < $group->size)
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
                        <flux:select.option @click="loading = true"
                                     wire:click="$parent.addContestant('{{ $group->id }}', '{{ $contestant->id }}')"
                                     dusk="select-contestant-{{ $loop->index }}" :value="$contestant->id">
                            {{ $contestant->getName() }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        @endif
    </div>
</div>
