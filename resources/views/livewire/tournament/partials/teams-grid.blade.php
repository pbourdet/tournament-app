@if($tournament->teams->isEmpty())
    <div class="text-gray-700 text-lg text-center mt-5">{{ __('No teams created yet') }}</div>
@else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1 mt-3">
        @foreach($tournament->teams as $team)
            <livewire:tournament.team-card :$organizerMode :$locked :$team :$tournament :key="$team->id . '-' . $locked"/>
        @endforeach
    </div>
@endif
