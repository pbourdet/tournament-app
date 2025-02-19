<div>
    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab icon="cog-6-tooth" name="settings">{{ __('Settings') }}</flux:tab>
            <flux:tab icon="network" name="matches">{{ __('Matches') }}</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="settings">
            @if(null === $eliminationPhase)
                <form class="space-y-6" wire:submit="create">
                    <flux:field>
                        <flux:label>{{ __('How many :contestants will compete in this phase ?', ['contestants' => $tournament->getContestantsTranslation(true)]) }}</flux:label>
                        <flux:select class="w-20!" dusk="number-of-contestants" wire:model="form.numberOfContestants">
                            @foreach([2, 4, 8, 16, 32, 64, 128] as $number)
                                @if($number <= $tournament->contestantsCount())
                                    <flux:option value="{{ $number }}">{{ $number }}</flux:option>
                                @endif
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:button variant="primary" dusk="create-elimination" type="submit">
                        {{ __('Confirm') }}
                    </flux:button>
                </form>
            @else
                <!-- TODO: Add the ability to edit the elimination phase settings -->
                <flux:icon.check-circle/>
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="matches">
            @include('livewire.tournament.partials.elimination-tree')
        </flux:tab.panel>
    </flux:tab.group>
</div>
