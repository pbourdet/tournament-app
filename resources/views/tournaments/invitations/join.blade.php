<div class="p-3 bg-gray-50 rounded-lg shadow-md">
    @if(null === $tournament)
        @include('tournaments.partials.join-error', ['errorMessage' => __('No tournament with this invitation code.')])
    @else
        <div class="flex justify-between">
            <div>
                <div class="font-bold">
                    {{ $tournament->name }}
                </div>
                <div class="text-xs italic text-gray-500">
                    {{__('Organized by')}} {{ $tournament->organizer->name }}
                </div>
            </div>

            <div title="{{ __('Player count') }}"
                 class="flex items-center bg-blue-100 rounded-xl py-0.5 px-2 font-bold text-blue-600 h-8 ml-1">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="mr-1 w-5 h-5">
                    <path
                        d="M4.5 6.375a4.125 4.125 0 1 1 8.25 0 4.125 4.125 0 0 1-8.25 0ZM14.25 8.625a3.375 3.375 0 1 1 6.75 0 3.375 3.375 0 0 1-6.75 0ZM1.5 19.125a7.125 7.125 0 0 1 14.25 0v.003l-.001.119a.75.75 0 0 1-.363.63 13.067 13.067 0 0 1-6.761 1.873c-2.472 0-4.786-.684-6.76-1.873a.75.75 0 0 1-.364-.63l-.001-.122ZM17.25 19.128l-.001.144a2.25 2.25 0 0 1-.233.96 10.088 10.088 0 0 0 5.06-1.01.75.75 0 0 0 .42-.643 4.875 4.875 0 0 0-6.957-4.611 8.586 8.586 0 0 1 1.71 5.157v.003Z"/>
                </svg>
                <span class="text-lg font-b">
                    {{ $tournament->players->count() }}/{{ $tournament->number_of_players }}
                </span>
            </div>
        </div>

        <div class="flex justify-center mt-4">
            @if($tournament->players->contains(Auth::user()))
                @include('tournaments.partials.join-error', ['errorMessage' => __('You are already taking part in this tournament.')])
            @elseif($tournament->isFull())
                @include('tournaments.partials.join-error', ['errorMessage' => __('This tournament is full.')])
            @else
                <form method="POST" action="{{ route('tournaments.join', ['tournament' => $tournament]) }}">
                    @csrf
                    <x-primary-button>
                        <span class="text-lg">{{ __('Join tournament') }}</span>
                    </x-primary-button>
                </form>
            @endif
        </div>
    @endif
</div>
