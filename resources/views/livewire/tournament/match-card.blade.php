<div>
    @if($match->getContestants()->isEmpty())
        <div class="relative flex flex-col border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-md w-60">
            <div class="flex justify-between items-center mb-2">
                <span class="italic text-sm font-medium">{{ __('TBD') }}</span>
            </div>
            <flux:separator class="my-2"/>
            <div class="flex justify-between items-center mt-2">
                <span class="italic text-sm font-medium">{{ __('TBD') }}</span>
            </div>
        </div>
    @else
        <div class="relative flex flex-col border border-zinc-200 dark:border-zinc-700 rounded-lg p-4 shadow-md w-60">
            @foreach($match->getContestants() as $contestant)
                <div class="flex justify-between items-center py-1">
                    @if($match->results->isNotEmpty())
                        @if($contestant->won($match))
                            <flux:icon name="check" class="text-green-500"/>
                        @elseif($contestant->lost($match))
                            <flux:icon name="x-mark" class="text-red-500"/>
                        @else
                            <flux:icon name="minus" class="text-gray-500 dark:text-gray-200"/>
                        @endif
                    @elseif($match->results->isNotEmpty())
                        <flux:icon name="x-mark" class="text-red-400"/>
                    @endif
                    <span class="text-sm font-medium overflow-hidden text-ellipsis">{{ $contestant->getName() }}</span>
                    <span class="text-sm font-bold">{{ $match->getResultFor($contestant)?->score }}</span>
                </div>
                @unless($loop->last)
                    <flux:separator class="my-2"/>
                @endunless
            @endforeach

            @can('manage', $match->round->phase->tournament)
                <flux:modal.trigger name="add-result-{{ $match->id }}">
                    <flux:button dusk="result-modal" icon="plus">{{ __('Results') }}</flux:button>
                </flux:modal.trigger>
            @endcan
        </div>
    @endif

        <flux:modal variant="flyout" class="space-y-6" name="add-result-{{ $match->id }}">
            <div>
                <flux:heading>{{ __('Add match result') }}</flux:heading>
                <flux:subheading>{{ __('Specify the outcome and the score') }}</flux:subheading>
            </div>

            <div class="space-y-4">
                @foreach($contestants as $key => $contestant)
                    <flux:input.group class="max-sm:flex-col">
                        <flux:input variant="filled" readonly wire:model="contestants.{{ $key }}.name"/>
                        <flux:select dusk="result-outcome-{{ $loop->index }}" variant="listbox" wire:model="contestants.{{ $key }}.outcome">
                            @foreach(\App\Enums\ResultOutcome::cases() as $outcome)
                                <flux:select.option dusk="outcome-{{ $loop->parent->index }}-{{ $outcome->value }}" value="{{ $outcome->value }}">{{ __($outcome->value) }}</flux:select.option>
                            @endforeach
                        </flux:select>
                        <flux:input dusk="result-score-{{ $loop->index }}" type="number" wire:model="contestants.{{ $key }}.score"/>
                    </flux:input.group>
                @endforeach
            </div>

            <flux:button variant="primary" dusk="add-result" wire:click="addResult">{{ __('Add') }}</flux:button>
        </flux:modal>
</div>
