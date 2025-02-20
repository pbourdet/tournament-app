<div x-data="{ editing: false, loading: false, locked: @js($locked) }"
     class="rounded-lg shadow-md border border-zinc-200 dark:border-zinc-900 mb-4">
    <div class="px-4 py-3 rounded-lg border-b border-zinc-200 dark:border-zinc-900 bg-zinc-50 dark:bg-zinc-700 flex items-center justify-between">
        <div class="flex flex-col truncate">
            <flux:heading class="truncate" x-show="!editing" size="lg" x-text="$wire.newName">{{ $team->name }}</flux:heading>
            <flux:input wire:model="newName" wire:keydown.enter="update('{{ $team->id }}')"
                        required minlength="2" maxlength="50"
                        x-cloak x-show="editing" x-ref="inputField" @keydown.enter="editing = false"/>
            @error('newName')
                <div class="text-red-600 text-xs">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center">
            @can('updateName', $team)
                <flux:button square x-show="!editing" dusk="edit-team-{{ $team->id }}"
                             @click="editing = true; setTimeout(() => $refs.inputField.focus(), 10)"
                             wire:loading.remove wire:target="update('{{ $team->id }}')">
                    <flux:icon.pencil class="size-5"/>
                </flux:button>
                <flux:button class="mr-2" square variant="primary" dusk="update-team-{{ $team->id }}"
                             x-cloak x-show="editing" @click="editing = false"
                             wire:click="update('{{ $team->id }}')">
                    <flux:icon.check class="size-5"/>
                </flux:button>
                <x-loader wire:loading wire:target="update('{{ $team->id }}')" class="m-2 size-6"/>

                <flux:button square x-cloak x-show="editing" @click="editing = false;">
                    <flux:icon.arrow-uturn-left class="size-5"/>
                </flux:button>
            @endcan
        </div>
    </div>
    <div class="px-4 py-3">
        <ul>
            @if($team->members->isEmpty())
                <li class="text-gray-400">No members</li>
            @else
                @foreach($team->members as $member)
                    <li x-data="{loading: false}" class="text-sm mb-2 flex items-center">
                        <img src="{{ Storage::url($member->getProfilePicture()) }}"
                             alt="{{ $member->username }}" class="w-6 h-6 rounded-full mr-2">
                        {{ $member->username }}
                        <flux:spacer/>
                        @if($organizerMode)
                            <flux:icon.loading x-cloak x-show="loading"/>
                            <flux:button inset x-show="!loading" @click="loading = true" :disabled="$locked" class="text-red-500!"
                                         icon="x-circle" variant="subtle" dusk="remove-member-{{ $loop->index }}"
                                         wire:click="$parent.removeMember('{{ $team->id }}', '{{ $member->id }}')"/>
                        @endif
                    </li>
                @endforeach
            @endif
        </ul>
        @if($organizerMode && $team->members->count() < $tournament->team_size)
            <div @if($team->members->isEmpty()) class="mt-5" @else class="mt-1" @endif x-data="{ loading: false }">
                <flux:icon.loading x-cloak class="m-auto" x-show="loading"/>
                <flux:select variant="listbox" searchable size="sm"
                             :placeholder="__('Add a player')"
                             dusk="select-members"
                             x-show="!loading" :disabled="$locked">
                    <x-slot name="search">
                        <flux:select.search placeholder="{{ __('Search player') }}"/>
                    </x-slot>
                    @foreach($selectablePlayers as $id => $selectablePlayer)
                        <flux:select.option @click="loading = true" wire:click="$parent.addMember('{{ $team->id }}', '{{ $id }}')" dusk="select-member-{{ $loop->index }}" :value="$id">
                            {{ $selectablePlayer }}
                        </flux:select.option>
                    @endforeach
                </flux:select>
            </div>
        @endif
    </div>
</div>
