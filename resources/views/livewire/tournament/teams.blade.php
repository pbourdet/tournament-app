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
                <livewire:tournament.team-card :$generationInProgress :$team :$tournament :key="$team->id . '-' . $generationInProgress"/>
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
