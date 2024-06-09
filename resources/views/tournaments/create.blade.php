<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create a tournament') }}
        </h2>
    </x-slot>

    <x-section>
        <div class="p-4 sm:p-8 max-w-xl">
            <form x-data="tournamentForm"
                  class="w-full flex content-start flex-col space-y-8"
                  action="{{ route('tournaments.store') }}"
                  method="POST">
                @csrf
                <div>
                    <x-input-label :mandatory="true" for="name" :value="__('Name')"/>
                    <x-text-input id="name" name="name" type="text" class="mt-1 w-full" required autofocus
                                  autocomplete="name"/>
                    <x-input-error class="mt-2" :messages="$errors->get('name')"/>
                </div>

                <div>
                    <x-input-label :mandatory="true" for="number_of_players" :value="__('Number of players')"/>
                    <x-text-input id="number_of_players" required name="number_of_players" max="256" min="2"
                                  type="number" class="mt-1 w-full" x-model.number="numberOfPlayers"
                                  @change="validateTeamSize"/>
                    <x-input-error class="mt-2" :messages="$errors->get('number_of_players')"/>
                </div>

                <div>
                    <x-input-label for="description" value="Description"/>
                    <textarea id="description" name="description"
                              class="w-full mt-1 rounded-md border-gray-300"></textarea>
                </div>

                <div>
                    <x-toggle :checked="true" :name="'join_tournament'">{{ __('Join tournament') }}</x-toggle>
                </div>

                <div>
                    <div>
                        <x-toggle @change="teamBased = !teamBased; validateTeamSize();" :checked="false"
                                  :name="'team_based'">{{ __('Team based') }}</x-toggle>
                    </div>
                    <div x-show="teamBased" class="mt-1 ml-5">
                        <x-input-label :mandatory="true" for="team_size" :value="__('Teams size')"/>
                        <x-text-input id="team_size" name="team_size" type="number" class="mt-0.5 w-full"
                                      x-model.number="teamSize"
                                      @change="validateTeamSize" x-bind:required="teamBased"/>
                        <x-input-error class="mt-2" :messages="$errors->get('team_size')">
                            <div class="text-sm text-red-500" x-show="teamSizeError">
                                {{  __('Team size must be greater than 1, less than the number of players and a multiple of the number of players.') }}
                            </div>
                        </x-input-error>
                    </div>
                </div>

                <div>
                    <x-primary-button x-bind:disabled="teamSizeError">{{ __('Create') }}</x-primary-button>
                </div>
            </form>

            <script>
                function tournamentForm() {
                    return {
                        teamBased: false,
                        teamSizeError: false,
                        numberOfPlayers: 0,
                        teamSize: 0,
                        validateTeamSize() {
                            if (false === this.teamBased) {
                                this.teamSizeError = false;

                                return;
                            }

                            if (this.numberOfPlayers % this.teamSize === 0
                                && this.teamSize > 1 && this.teamSize !== this.numberOfPlayers
                                && this.numberOfPlayers !== 0
                            ) {
                                this.teamSizeError = false;

                                return;
                            }

                            this.teamSizeError = true;
                        }
                    }
                }
            </script>
        </div>
    </x-section>
</x-app-layout>
