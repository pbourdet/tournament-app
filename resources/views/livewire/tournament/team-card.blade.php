<div x-data="{ editing: false, loading: false, locked: @js($locked) }"
     class="max-w-sm rounded-lg shadow-md border border-gray-200 bg-white mb-4">
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
        <div class="flex flex-col truncate">
            <flux:heading class="truncate" x-show="!editing" size="lg" x-text="$wire.newName">{{ $team->name }}</flux:heading>
            <flux:input wire:model="newName" wire:keydown.enter="update('{{ $team->id }}')"
                        required minlength="2" maxlength="50"
                        x-cloak x-show="editing" x-ref="inputField" @keydown.enter="editing = false"/>
            @error('newName')
                <div class="text-red-600 text-xs">{{ $message }}</div>
            @enderror
        </div>

        <div class="flex items-center space-x-2 ml-2">
            @can('manage', $tournament)
                <flux:button square x-show="!editing"
                             @click="editing = true; setTimeout(() => $refs.inputField.focus(), 10)"
                             wire:loading.remove wire:target="update('{{ $team->id }}')">
                    <flux:icon.pencil class="size-5"/>
                </flux:button>
                <flux:button square variant="primary"
                             x-cloak x-show="editing" @click="editing = false"
                             wire:click="update('{{ $team->id }}')">
                    <flux:icon.check class="size-5"/>
                </flux:button>
                <x-loader wire:loading wire:target="update('{{ $team->id }}')" class="size-4"/>

                <flux:button square x-cloak x-show="editing" @click="editing = false;">
                    <flux:icon.arrow-uturn-left class="size-5"/>
                </flux:button>

                <flux:button square variant="danger"
                             x-bind:disabled="loading || locked" x-show="!editing"
                             @click="loading = true"
                             wire:click="$parent.delete('{{ $team->id }}')"
                             wire:confirm="Confirm deletion of {{ $team->name }}">
                    <flux:icon.trash x-show="!loading" class="size-5"/>
                    <x-loader x-cloak x-show="loading" class="size-5"/>
                </flux:button>
            @endcan
        </div>
    </div>
    <div class="px-4 py-3">
        <ul class="space-y-1">
            @foreach($team->members as $member)
                <li class="text-gray-700 text-sm flex items-center">
                    <img src="{{ Storage::url($member->getProfilePicture()) }}"
                         alt="{{ $member->username }}" class="w-6 h-6 rounded-full mr-2">
                    {{ $member->username }}
                </li>
            @endforeach
        </ul>
    </div>
</div>
