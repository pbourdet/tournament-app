<div x-data="{generationStarted: false, locked: @js($locked)}" class="space-y-6">
    @if(!$tournament->team_based)
        <div class="space-y-6">
            <flux:heading>
                {{ __('Your tournament is not played in teams. You can change this setting in the "General" tab.') }}
            </flux:heading>
            <div>
                <flux:link class="text-sm" wire:navigate href="{{ route('tournaments.organize', ['tournament' => $tournament, 'page' => 'general']) }}">
                    {{ __('Go to general settings') }}
                </flux:link>
            </div>
        </div>
    @else
        <div class="flex justify-between items-center">
            <div class="flex flex-col gap-2 sm:flex-row">
                <div class="mr-2">
                    <flux:modal.trigger name="create-team" dusk="create-team-modal">
                        <flux:button :disabled="$tournament->hasAllTeams() || $this->locked">
                            {{ __('Create team') }}
                        </flux:button>
                    </flux:modal.trigger>
                </div>
                @if($tournament->canGenerateTeams())
                    <div wire:replace class="flex items-center">
                        <flux:button x-on:click="generationStarted = true" variant="primary"
                                     x-bind:disabled="generationStarted || locked"
                                     type="button" wire:click="generate" dusk="generate-teams">
                            <span>{{ __('Generate teams') }}</span>
                        </flux:button>
                        <flux:tooltip toggleable>
                            <flux:button size="sm" variant="ghost" icon="information-circle"/>

                            <flux:tooltip.content>
                                <p>{{ __('This will generate the remaining teams randomly.') }}</p>
                                <p>{{ __('Some other actions such as deleting teams will be prevented during this process') }}</p>
                            </flux:tooltip.content>
                        </flux:tooltip>
                    </div>
                @endif
            </div>
            <flux:badge icon-trailing="users" variant="pill" color="teal">
                <span class="font-bold">{{ $tournament->teams()->count() }}/{{$tournament->maxTeamsCount()}}</span>
            </flux:badge>
        </div>

        @include('livewire.tournament.partials.teams-grid')

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
                                         clear="close" dusk="select-members"
                                         @change="if ($wire.createForm.members.length === {{ $tournament->team_size }}) $dispatch('click')"
                                         wire:model="createForm.members">
                                <x-slot name="search">
                                    <flux:select.search placeholder="{{ __('Search player') }}"/>
                                </x-slot>
                                @foreach($selectablePlayers as $id => $selectablePlayer)
                                    <flux:option dusk="select-member-{{ $loop->index }}" x-bind:disabled="$wire.createForm.members.length >= {{ $tournament->team_size }}" :value="$id">{{ $selectablePlayer }}</flux:option>
                                @endforeach
                            </flux:select>
                        </flux:field>
                        <div class="flex items-center mt-4">
                            <flux:button variant="primary" type="submit" :loading="false"
                                         wire:loading.attr="disabled" dusk="create-team"
                                         x-bind:disabled="$wire.createForm.members.length !== {{ $tournament->team_size }}">
                                {{ __('Create team') }}
                            </flux:button>
                            <x-loader wire:loading wire:target="create" class="size-5 ml-1"/>
                        </div>
                    </form>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
