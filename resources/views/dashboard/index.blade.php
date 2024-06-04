<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @if($managedTournaments->isNotEmpty())
        <x-section>
            @include('dashboard.partials.tournaments-list', [
                'tournaments' => $managedTournaments,
                'title' =>  __('Your Hosted Tournaments'),
            ])
        </x-section>
    @endif

    @if($tournaments->isNotEmpty())
        <x-section class="@if($managedTournaments->isNotEmpty()) mt-4 @endif">
            @include('dashboard.partials.tournaments-list', [
                'tournaments' => $tournaments,
                'title'=> __('Your Joined Tournaments')
            ])
        </x-section>
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
</x-app-layout>
