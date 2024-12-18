<div x-data="{generationStarted: false, locked: @js($locked)}">
    <div class="flex justify-between items-center">
        <div class="flex flex-col sm:flex-row">
            @can('manage', $tournament)
                <div class="mr-2">
                    <flux:modal.trigger name="create-team">
                        <flux:button :disabled="$tournament->hasAllTeams() || $this->locked">
                            {{ __('Create team') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
                @if($tournament->canGenerateTeams())
                    <div wire:replace class="flex items-center">
                        <flux:button x-on:click="generationStarted = true" variant="primary"
                                     x-bind:disabled="generationStarted || locked"
                                     type="button" wire:click="generate" id="button-generate-teams">
                            <span>{{ __('Generate teams') }}</span>
                        </flux:button>
                        <flux:tooltip content="{{ __('This will generate the remaining teams randomly. Some other actions such as deleting teams will be prevented during this process') }}">
                            <flux:icon.information-circle class="text-zinc-500"/>
                        </flux:tooltip>
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
                <livewire:tournament.team-card :$locked :$team :$tournament :key="$team->id . '-' . $locked"/>
            @endforeach
        </div>
    @endif

    <flux:modal class="w-5/6 sm:w-2/3 md:w-1/2 lg:w-1/3" name="create-team">
        <div>
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg text-gray-900">{{ __('Create new team') }}</h2>
            </div>
            <div>
                <form x-data="{ members: [] }" wire:submit="create">
                    <div class="mb-4">
                        <flux:input wire:model="createForm.name" label="{{ __('Team name') }}"/>
                    </div>

                    <flux:field>
                        <flux:label>{{ __('Team members') }}</flux:label>
                        <flux:select variant="listbox" searchable multiple selected-suffix="{{ __('users selected') }}"
                                     :placeholder="__('Select :count players', ['count' => $tournament->team_size])"
                                     clear="close"
                                     @change="if ($wire.createForm.members.length === {{ $tournament->team_size }}) $dispatch('click')"
                                     wire:model="createForm.members">
                            <x-slot name="search">
                                <flux:select.search name="players-search"/>
                            </x-slot>
                            @foreach($selectablePlayers as $id => $selectablePlayer)
                                <flux:option x-bind:disabled="$wire.createForm.members.length >= {{ $tournament->team_size }}" :value="$id">{{ $selectablePlayer }}</flux:option>
                            @endforeach
                        </flux:select>
                    </flux:field>
                    <div class="flex items-center mt-4">
                        <flux:button variant="primary" type="submit" :loading="false"
                                     wire:loading.attr="disabled"
                                     x-bind:disabled="$wire.createForm.members.length !== {{ $tournament->team_size }}">
                            {{ __('Create team') }}
                        </flux:button>
                        <x-loader wire:loading wire:target="create" class="size-5 ml-1"/>
                    </div>
                </form>
            </div>
        </div>
    </flux:modal>
</div>
