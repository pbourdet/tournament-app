<div class="overflow-x-auto bg-white shadow-md rounded-lg mt-5">
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
        <tr>
            <th></th>
            <th class="pr-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Player') }}</th>
            <th></th>
            @can('manage', $tournament)
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Actions') }}</th>
            @endcan
        </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
        @foreach($players as $player)
            <tr class="hover:bg-gray-100" wire:key="{{ $player->id }}">
                <td class="w-16 pl-4 py-4">
                    <img src="{{ Storage::url($player->getProfilePicture()) }}"
                         alt="{{ $player->username }}"
                         class="w-8 h-8 rounded-full">
                </td>
                <td class="pr-6 py-4">
                    @if($player->is(Auth::user()))
                        <span class="text-sm font-medium text-indigo-600">{{ __('You') }}</span>
                    @else
                        <span class="text-sm font-medium text-gray-900">{{ $player->username }}</span>
                    @endif
                </td>
                <td class="w-12 px-4 py-4 text-center text-amber-600">
                    @if($tournament->organizer->is($player))
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                             class="size-5">
                            <path
                                d="M11.562 3.266a.5.5 0 0 1 .876 0L15.39 8.87a1 1 0 0 0 1.516.294L21.183 5.5a.5.5 0 0 1 .798.519l-2.834 10.246a1 1 0 0 1-.956.734H5.81a1 1 0 0 1-.957-.734L2.02 6.02a.5.5 0 0 1 .798-.519l4.276 3.664a1 1 0 0 0 1.516-.294z"/>
                            <path d="M5 21h14"/>
                        </svg>
                    @endif
                </td>
                @can('manage', $tournament)
                    <td class="px-6 py-4 min-h-full">
                        <div class="flex items-center min-h-full">
                            <x-danger-button :disabled="$generationInProgress"
                                             wire:click="removePlayer('{{ $player->id }}')"
                                             wire:loading.remove
                                             wire:target="removePlayer('{{ $player->id }}')">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                                     stroke="currentColor" class="size-4">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0"/>
                                </svg>
                            </x-danger-button>
                            <x-loader wire:target="removePlayer('{{ $player->id }}')"
                                      wire:loading
                                      class="ml-2 size-5"/>
                        </div>
                    </td>
                @endcan
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
