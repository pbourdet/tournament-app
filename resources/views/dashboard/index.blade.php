<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        @if($managedTournaments->isNotEmpty())
            @include('dashboard.partials.tournaments-list', [
                'tournaments' => $managedTournaments,
                'title' =>  __('Your Hosted Tournaments'),
            ])
        @endif

        @if($tournaments->isNotEmpty())
            <div class="@if($managedTournaments->isNotEmpty()) py-4 @endif">
                @include('dashboard.partials.tournaments-list', [
                    'tournaments' => $tournaments,
                    'title'=> __('Your Joined Tournaments')
                ])
            </div>
        @endif

        @if($managedTournaments->isEmpty() && $tournaments->isEmpty())
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
                    <div class="p-6 text-center text-gray-500">
                        {{ __('No tournaments') }}
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>
