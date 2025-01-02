@props(['match'])

@if($match->getContestants()->isEmpty())
    <div class="relative flex flex-col border border-gray-300 rounded-lg p-4 shadow-md w-60 bg-white">
        <div class="flex justify-between items-center mb-2">
            <span class="italic text-gray-400 text-sm font-medium">{{ __('TBD') }}</span>
        </div>
        <flux:separator class="my-2"/>
        <div class="flex justify-between items-center mt-2">
            <span class="italic text-gray-400 text-sm font-medium">{{ __('TBD') }}</span>
        </div>
    </div>
@else
    <div class="relative flex flex-col border border-gray-300 rounded-lg p-4 shadow-md w-60 bg-white">
        @foreach($match->getContestants() as $contestant)
            <div class="flex justify-between items-center py-1">
                <span class="text-sm font-medium text-gray-700 overflow-hidden text-ellipsis">{{ $contestant->name }}</span>
                <span class="text-sm font-bold text-gray-800">0</span>
            </div>
            @unless($loop->last)
                <flux:separator class="my-2"/>
            @endunless
        @endforeach
    </div>
@endif
