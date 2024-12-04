<div x-data="{ editing: false, loading: false, generationInProgress: @js($generationInProgress) }"
     class="max-w-sm rounded-lg shadow-md border border-gray-200 bg-white mb-4">
    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
        <div class="flex flex-col truncate">
            <h2 x-show="!editing" x-text="$wire.newName"
                class="font-semibold text-lg text-gray-800 truncate leading-none"></h2>
            <x-text-input type="text" placeholder="Team Name" class="border border-gray-300 rounded px-2 py-1 text-sm"
                          required minlength="2" maxlength="50"
                          wire:model="newName" wire:keydown.enter="update('{{ $team->id }}')"
                          x-show="editing" x-ref="inputField" @keydown.enter="editing = false"/>
            @error('newName')
            <div class="text-red-600 text-xs">{{ $message }}</div> @enderror
        </div>

        <div class="flex items-center space-x-2">
            @can('manage', $tournament)
                <x-secondary-button wire:loading.remove wire:target="update('{{ $team->id }}')" x-show="!editing"
                                    type="button" class="text-gray-500 hover:text-gray-700"
                                    @click="editing = true; setTimeout(() => $refs.inputField.focus(), 10)">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L6.832 19.82a4.5 4.5 0 0 1-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 0 1 1.13-1.897L16.863 4.487Zm0 0L19.5 7.125"/>
                    </svg>
                </x-secondary-button>

                <x-primary-button x-show="editing" type="button" @click="editing = false"
                                  wire:click="update('{{ $team->id }}')">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5"/>
                    </svg>
                </x-primary-button>
                <x-loader wire:loading wire:target="update('{{ $team->id }}')" class="size-4"/>

                <x-secondary-button type="button"
                                    x-show="editing" @click="editing = false;">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3"/>
                    </svg>
                </x-secondary-button>

                <x-danger-button type="button"
                                 x-bind:disabled="loading || generationInProgress" x-show="!editing"
                                 @click="loading = true"
                                 wire:click="$parent.delete('{{ $team->id }}')"
                                 wire:confirm="Confirm deletion of {{ $team->name }}">
                    <svg x-show="!loading"
                         xmlns="http://www.w3.org/2000/svg"
                         fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="size-4">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                    </svg>
                    <x-loader x-show="loading" class="size-4"/>
                </x-danger-button>
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
