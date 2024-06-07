<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create a tournament') }}
        </h2>
    </x-slot>

    <x-section>
        <div class="p-4 sm:p-8 max-w-xl">
            <form class="w-full flex content-start flex-col space-y-8" action="{{ route('tournaments.store') }}"
                  method="POST">
                @csrf
                <div>
                    <x-input-label for="name" :value="__('Name')"/>
                    <x-text-input id="name" name="name" type="text" class="mt-1 w-full" required autofocus
                                  autocomplete="name"/>
                    <x-input-error class="mt-2" :messages="$errors->get('name')"/>
                </div>

                <div>
                    <x-input-label for="number_of_players" :value="__('Number of players')"/>
                    <x-text-input id="number_of_players" required name="number_of_players" max="256" min="2"
                                  type="number" class="mt-1 w-full"/>
                    <x-input-error class="mt-2" :messages="$errors->get('number_of_players')"/>
                </div>

                <div>
                    <x-input-label for="description" value="Description"/>
                    <textarea id="description" name="description"
                              class="w-full mt-1 rounded-md border-gray-300"></textarea>
                </div>

                <div>
                    <x-toggle :name="'join_tournament'">{{ __('Join tournament') }}</x-toggle>
                </div>

                <div>
                    <x-primary-button>{{ __('Create') }}</x-primary-button>
                </div>
            </form>
        </div>
    </x-section>
</x-app-layout>
