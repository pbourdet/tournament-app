<div>
    @if($tournament->organizer->is(auth()->user()) && $tournament->canGenerateTeams())
        <div>
            <x-primary-button type="button" wire:click="generate" id="button-generate-teams">
                {{ __('Generate teams') }}
            </x-primary-button>
        </div>
    @endif
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-1 mt-3">
        @foreach($tournament->teams as $team)
            <div wire:key="{{ $team->id }}" class="max-w-sm rounded-lg shadow-md border border-gray-200 bg-white mb-4">
                <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                    <h2 class="font-semibold text-lg text-gray-800 truncate">{{ $team->name }}</h2>
                    @can('manage', $tournament)
                        <x-danger-button wire:loading.attr="disabled" wire:target="delete('{{ $team->id }}')" type="button" wire:click="delete('{{ $team->id }}')">
                            <svg wire:loading.remove wire:target="delete('{{ $team->id }}') xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-4">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                            </svg>
                            <x-loader wire:loading wire:target="delete('{{ $team->id }}')" class="w-4 h-4 text-gray-200 animate-spin fill-blue-600"/>
                        </x-danger-button>
                    @endcan
                </div>
                <div class="px-4 py-3">
                    <ul class="space-y-1">
                        @foreach($team->members as $member)
                            <li class="text-gray-700 text-sm flex items-center">
                                <img src="{{ Storage::url($member->getProfilePicture()) }}" alt="{{ $member->name }}" class="w-6 h-6 rounded-full mr-2">
                                {{ $member->name }}
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endforeach
    </div>
</div>
