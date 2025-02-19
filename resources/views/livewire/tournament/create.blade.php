<x-slot name="header">
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Create a tournament') }}
    </h2>
</x-slot>

<x-section class="md:w-2/3 xl:w-1/2">
    <div class="min-h-72 flex flex-col justify-between item p-4" x-data>
        <div class="grow">
            @if($currentStep === 1)
                <div>
                    <x-input-label class="text-xl!" for="name"
                                   :value="__('Start by choosing a name for your tournament').'...'"/>
                    <x-text-input id="name" name="name" type="text" class="mt-3 w-full"
                                  wire:model="form.name"
                                  required autofocus autocomplete="name"/>
                    <x-input-error class="mt-1" :messages="$errors->get('form.name')"/>
                </div>

                <div class="mt-5">
                    <x-input-label :mandatory="false" for="description" :value="'... '.__('and a description')"/>
                    <textarea id="description" name="description" maxlength="1000"
                              wire:model="form.description"
                              class="w-full mt-1 rounded-md border-gray-300 resize-y max-h-56 overflow-auto">
                        </textarea>
                    <x-input-error class="mt-1" :messages="$errors->get('form.description')"/>
                </div>
            @endif

            @if($currentStep === 2)
                <x-input-label class="text-xl!" for="number_of_players"
                               :value="__('How many players will participate in your tournament ?')"/>
                <x-text-input id="number_of_players" required name="number_of_players" max="256" min="2" focus
                              type="number" class="mt-3 w-1/6"
                              wire:model="form.numberOfPlayers"/>
                <x-input-error class="mt-1" :messages="$errors->get('form.numberOfPlayers')"/>
            @endif

            @if($currentStep === 3)
                <div>
                    <x-input-label class="text-xl!" :value="__('Will your tournament be played in teams ?')"/>
                    <div class="mt-5 flex items-center">
                        <span class="mr-2">{{ __('No') }}</span>
                        <x-toggle :livewire="true" :model="'form.teamBased'" :name="'team_based'"/>
                        <span class="ml-1">{{ __('Yes') }}</span>
                    </div>
                </div>
                <div class="mt-4" x-show="$wire.form.teamBased">
                    <x-input-label for="team_size"
                                   :value="__('How many players will be in each team ?')"/>
                    <x-text-input id="team_size" name="team_size" type="number" class="mt-0.5 w-1/6"
                                  wire:model="form.teamSize" min="2" max="{{ ceil($form->numberOfPlayers / 2) }}"/>
                    <x-input-error class="mt-1" :messages="$errors->get('form.teamSize')"/>
                </div>
            @endif

            @if($currentStep === 4)
                <x-input-label class="text-xl!"
                               :value="__('Finally, do you also want to participate in your tournament ?')"/>
                <div class="mt-5 flex items-center">
                    <span class="mr-2">{{ __('No') }}</span>
                    <x-toggle :livewire="true" :model="'form.joinTournament'" :name="'join_tournament'"/>
                    <span class="ml-1">{{ __('Yes') }}</span>
                </div>
            @endif
        </div>


        <!-- Buttons -->
        <div wire:replace class="flex justify-between mt-4">
            <x-secondary-button x-show="$wire.currentStep > 1" :disabled="$currentStep <= $firstStep"
                                wire:click="previous">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                         stroke="currentColor" class="mr-1 size-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5"/>
                </svg>
                <span>{{ __('Back') }}</span>
            </x-secondary-button>

            <div class="ml-auto">
                @if($currentStep === $lastStep)
                    <x-primary-button id="save-button" wire:click="save">
                        <span>{{ __('Create tournament') }}</span>
                    </x-primary-button>
                @else
                    <x-primary-button id="next-button" wire:click="next">
                        <span>{{ __('Next') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="ml-1 size-4">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5"/>
                        </svg>
                    </x-primary-button>
                @endif
            </div>
        </div>
    </div>
</x-section>
