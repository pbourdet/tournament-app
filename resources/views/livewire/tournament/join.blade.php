<flux:modal :dismissible="true" name="new-tournament" class="md:w-96 space-y-6">
    <div>
        <div>
            <flux:heading>{{ __('Join a tournament') }}</flux:heading>
            <flux:subheading>{{ __('Enter the tournament invitation code') }}</flux:subheading>
        </div>
        <div class="mt-6 flex justify-center items-center">
            <div class="flex items-center">
                <flux:input placeholder="{{ mb_strtoupper(Str::random(6)) }}..." maxlength="6" name="invitation-code"
                            class:input="uppercase text-center text-2xl! w-40!"
                            wire:model="tournamentCode"
                            @input.debounce="if ($event.target.value.length === 6) $wire.find()"/>
                <x-loader wire:loading wire:target="find" class="absolute size-8 right-9 md:right-18"/>
            </div>
        </div>
        @if($showResponse)
            <div class="mt-2 w-100" wire:loading.class="opacity-50">
                <div class="p-3 bg-zinc-50 dark:bg-zinc-700 rounded-lg shadow-md">
                    @if(null === $tournament)
                        @include('livewire.tournament.partials.join-error', ['errorMessage' => __('No tournament with this invitation code.')])
                    @else
                        <div>
                            <div class="flex justify-between">
                                <div class="font-bold">
                                    {{ $tournament->name }}
                                </div>
                                <flux:badge icon="users" variant="pill" color="blue" inset>
                                    {{ $tournament->players->count() }}/{{ $tournament->number_of_players }}
                                </flux:badge>
                            </div>
                            <div class="text-xs italic text-gray-500">
                                {{__('Organized by')}} {{ $tournament->organizer->username }}
                            </div>
                        </div>

                        <div class="flex justify-center mt-4">
                            @if($tournament->players->contains($this->user))
                                @include('livewire.tournament.partials.join-error', ['errorMessage' => __('You are already taking part in this tournament.')])
                            @elseif($tournament->isFull())
                                @include('livewire.tournament.partials.join-error', ['errorMessage' => __('This tournament is full.')])
                            @else
                                <flux:button dusk="join-tournament" variant="primary" wire:click="join('{{ $tournament->id }}')">
                                    <span class="text-lg">{{ __('Join tournament') }}</span>
                                </flux:button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        @endif
    </div>

    <flux:separator text="{{ __('or') }}"/>

    <div class="text-center">
        @can('create', App\Models\Tournament::class)
            <flux:button variant="primary" href="{{ route('tournaments.create') }}" wire:navigate>
                {{ __('Create a tournament') }}
            </flux:button>
        @else
            <x-input-error class="mt-1" :messages="__('You cannot create more tournaments')"/>
        @endcan
    </div>
</flux:modal>
