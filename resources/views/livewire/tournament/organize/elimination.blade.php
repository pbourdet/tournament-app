<div>
    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab icon="cog-6-tooth" name="settings">{{ __('Settings') }}</flux:tab>
            <flux:tab icon="network" name="matches">{{ __('Matches') }}</flux:tab>
        </flux:tabs>

        <flux:tab.panel name="settings">
            @if(null === $eliminationPhase)
                <div class="space-y-6">
                    <flux:field>
                        <flux:label>{{ __('How many :contestants will compete in this phase ?', ['contestants' => $tournament->getContestantsTranslation(true)]) }}</flux:label>
                        <flux:select class="w-20!" dusk="number-of-contestants" wire:model="form.numberOfContestants">
                            @foreach([2, 4, 8, 16, 32, 64, 128] as $number)
                                @if($number <= $tournament->contestantsCount())
                                    <flux:select.option value="{{ $number }}">{{ $number }}</flux:select.option>
                                @endif
                            @endforeach
                        </flux:select>
                    </flux:field>

                    <flux:button wire:click="create" :disabled="$this->user->cannot('create', [\App\Models\EliminationPhase::class, $tournament])" variant="primary" dusk="create-elimination">
                        {{ __('Confirm') }}
                    </flux:button>
                </div>
            @else
                <!-- TODO: Add the ability to edit the elimination phase settings -->
                <flux:icon.check-circle/>
                <flux:button variant="danger" dusk="delete-elimination-phase" wire:click="deletePhase('{{ $eliminationPhase->id }}')">
                    {{ __('Remove phase') }}
                </flux:button>
            @endif
        </flux:tab.panel>
        <flux:tab.panel name="matches">
            @include('livewire.tournament.partials.elimination-tree')
        </flux:tab.panel>
    </flux:tab.group>
</div>
