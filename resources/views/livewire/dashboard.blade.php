<x-slot name="title">{{ __('Dashboard') }} - {{ config('app.name', 'Laravel') }}</x-slot>

<flux:main>
    @if($managedTournaments->isNotEmpty())
        <x-section>
            @include('dashboard.partials.tournaments-list', [
                'tournaments' => $managedTournaments,
                'title' =>  __('Your Hosted Tournaments'),
            ])
        </x-section>
    @endif

    @if($joinedTournaments->isNotEmpty())
        <x-section>
            @include('dashboard.partials.tournaments-list', [
                'tournaments' => $joinedTournaments,
                'title'=> __('Your Joined Tournaments')
            ])
        </x-section>
    @endif

    @if($managedTournaments->isEmpty() && $joinedTournaments->isEmpty())
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                <div class="p-6 text-center text-gray-500">
                    {{ __('No tournaments') }}
                </div>
            </div>
        </div>
    @endif
</flux:main>
