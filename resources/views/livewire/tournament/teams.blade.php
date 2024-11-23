<div>
    <div class="flex justify-between items-center">
        <div class="flex">
            @can('manage', $tournament)
                <div class="mr-2">
                    <x-secondary-button :disabled="$tournament->hasAllTeams() || $this->generationInProgress"
                                        x-on:click.prevent="$dispatch('open-modal', 'create-team')">
                        <span>{{ __('Create team') }}</span>
                    </x-secondary-button>
                </div>
                @if($tournament->canGenerateTeams())
                    <div class="flex items-center">
                        <x-primary-button wire:loading.attr="disabled" :disabled="$this->generationInProgress"
                                          type="button" wire:click="generate" id="button-generate-teams">
                            <span>{{ __('Generate teams') }}</span>
                        </x-primary-button>
                        <x-loader wire:loading wire:target="generate" class="size-5 ml-1"/>
                    </div>
                @endif
            @endcan
        </div>
        <div class="bg-gray-100 text-gray-800 text-sm font-medium rounded-full px-2 py-1 shadow-sm">
            <span>{{ __('Teams') }} :</span>
            <span class="font-bold">{{ $tournament->teams()->count() }}/{{$tournament->maxTeamsCount()}}</span>
        </div>
    </div>
    @if($tournament->teams->isEmpty())
        <div class="text-gray-700 text-lg text-center mt-5">{{ __('No teams created yet') }}</div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1 mt-3">
            @foreach($tournament->teams as $team)
                <div wire:key="{{ $team->id }}"
                     class="max-w-sm rounded-lg shadow-md border border-gray-200 bg-white mb-4">
                    <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <h2 class="font-semibold text-lg text-gray-800 truncate">{{ $team->name }}</h2>
                        @can('manage', $tournament)
                            <x-danger-button type="button" :disabled="$this->generationInProgress"
                                             wire:loading.attr="disabled" wire:target="delete('{{ $team->id }}')"
                                             wire:click="delete('{{ $team->id }}')"
                                             wire:confirm="Confirm deletion of {{ $team->name }}"
                            >
                                <svg wire:loading.remove wire:target="delete('{{ $team->id }}') xmlns=" http:
                                //www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                      d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                </svg>
                                <x-loader wire:loading wire:target="delete('{{ $team->id }}')" class="size-4"/>
                            </x-danger-button>
                        @endcan
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
            @endforeach
        </div>
    @endif

    <x-modal :overflowable="true" name="create-team" focusable>
        <div x-data>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg text-gray-900">{{ __('Create new team') }}</h2>
                <x-close-modal-button/>
            </div>
            <div>
                <form x-data="{ members: [] }" wire:submit="create">
                    <div class="mb-4">
                        <x-input-label for="team-name" value="{!! __('Team name') !!}"/>
                        <x-text-input wire:model="createForm.name" id="team-name" label="{!! __('Team name') !!}"
                                      placeholder="{{ __('Team name') }}..."/>
                        <x-input-error :messages="$errors->get('createForm.name')" class="mt-1"/>
                    </div>

                    <div @selection-added.window="members = [...members, $event.detail.id]"
                         @selection-removed.window="members = members.filter(id => id !== $event.detail.id)"
                         @team-created.window="members = []"
                         x-effect="$wire.createForm.members = members">
                        <x-input-label :mandatory="true" for="team-members" value="{!! __('Team members') !!}"/>
                        <x-multi-select-dropdown :reset-event="'team-created'"
                                                 :placeholder="__('Select :count players', ['count' => $tournament->team_size])"
                                                 :options="$selectablePlayers"
                                                 :selections-count="$tournament->team_size"/>
                        <x-input-error :messages="$errors->get('createForm.members')" class="mt-1"/>
                    </div>

                    <div class="flex items-center mt-4">
                        <x-primary-button wire:loading.attr="disabled" x-bind:disabled="members.length < {{ $tournament->team_size }}" type="submit">
                            <span>{{ __('Create team') }}</span>
                        </x-primary-button>
                        <x-loader wire:loading wire:target="create" class="size-5 ml-1"/>
                    </div>
                </form>
            </div>
        </div>
    </x-modal>
</div>
