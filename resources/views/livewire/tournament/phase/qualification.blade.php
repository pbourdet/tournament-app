<div class="w-full 2xl:w-2/3 space-y-6">
    @if(null === $tournament->groupPhase)
        <div>
            <flux:heading size="lg">{{ __('Create Qualification Phase') }}</flux:heading>
            <flux:subheading>{{ __('Chose a format and custom it to your needs.') }}</flux:subheading>
        </div>
        <flux:radio.group wire:model="type" label="{{ __('Format') }}" variant="cards" class="max-sm:flex-col">
            <div class="w-52">
                <flux:radio value="{{ \App\Enums\PhaseType::GROUP }}" checked>
                    <flux:radio.indicator />

                    <div class="flex-1">
                        <flux:heading class="leading-4">{{ __('Groups') }}</flux:heading>
                    </div>
                </flux:radio>
            </div>
        </flux:radio.group>

        <div x-cloak x-transition:enter x-show="$wire.type === '{{ \App\Enums\PhaseType::GROUP }}'" class="space-y-6">

            <flux:subheading class="flex items-center">
                <flux:icon.information-circle class="mr-2"/>
                {{ __('Contestants are divided into groups and compete in a round-robin format, earning points based on performance. The top teams from each group advance to elimination phase. Examples include the FIFA World Cup, UEFA Champions League, and ICC Cricket World Cup.') }}
            </flux:subheading>
            <flux:card class="shadow-sm space-y-6" x-data="groupPhase({{ $tournament->contestantsCount() }})">
                <flux:heading size="lg">{{ __('Settings') }}</flux:heading>

                <div class="space-y-4">
                    <div class="flex max-sm:flex-col">
                        <flux:input class="!w-20 max-sm:mb-4" label="{{ __('Number of groups') }}" type="number"
                                    wire:model="groupForm.numberOfGroups" min="2" :max="$tournament->contestantsCount() / 2"
                                    @input="calculateGroups()" dusk="input-number-of-groups"/>
                        <flux:separator vertical class="mx-3"/>
                        <flux:input class="!w-20" label="{{ __('Contestants qualifying per group') }}" type="number"
                                    wire:model="groupForm.contestantsQualifying"
                                    @input="computeContestantsQualifying()"
                                    min="1" x-bind:max="Math.min(...groups)" dusk="input-contestants-qualifying"/>
                    </div>
                    <div>
                        <p>{{ __('Total contestants qualifying') }} : <span x-text="groups.length * $wire.groupForm.contestantsQualifying"></span></p>
                        <p>{{ __('Total matches') }} : <span x-text="computeMatches()"></span></p>
                    </div>
                    <div x-bind:class="groups.length <= 3 ? '2xl:grid-cols-3' : '2xl:grid-cols-' + Math.ceil(Math.sqrt(groups.length))"
                         class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-1 mt-3">
                        <template x-for="(group, index) in groups" :key="index">
                            <div class="px-3 py-1 border rounded-lg bg-gray-50">
                                <h3 class="font-bold text-lg text-gray-800">{{ __('Group') }} <span x-text="index + 1"></span></h3>
                                <div class="grid grid-cols-4 mt-2">
                                    <template x-for="(contestant, contestantIndex) in group" :key="contestantIndex">
                                        <div>
                                            <flux:badge x-show="contestantIndex >= $wire.groupForm.contestantsQualifying" icon-trailing="{{ $tournament->team_based ? 'users' : 'user' }}"
                                                        variant="pill" class="m-1 opacity-50">
                                                <span x-text="@js($tournament->team_based) ? 'T' + contestant : 'P' + contestant"></span>
                                            </flux:badge>
                                            <flux:badge x-show="contestantIndex < $wire.groupForm.contestantsQualifying" icon-trailing="{{ $tournament->team_based ? 'users' : 'user' }}"
                                                        color="lime" variant="pill" class="m-1">
                                                <span x-text="@js($tournament->team_based) ? 'T' + contestant : 'P' + contestant"></span>
                                            </flux:badge>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <flux:button variant="primary" dusk="create-group-phase" wire:click="create">{{ __('Save') }}</flux:button>
            </flux:card>

            <script>
                function groupPhase(totalContestants) {
                    return {
                        totalContestants,
                        groups: [],
                        computeMatches() {
                            return this.groups.reduce((sum, teams) => sum + (teams * (teams - 1)) / 2, 0);
                        },
                        computeContestantsQualifying() {
                            if (this.$wire.groupForm.contestantsQualifying > Math.min(...this.groups)) {
                                this.$wire.groupForm.contestantsQualifying = Math.min(...this.groups);
                            }
                        },
                        calculateGroups() {
                            let groupCount = this.$wire.groupForm.numberOfGroups;

                            if (groupCount > this.totalContestants / 2) {
                                groupCount = Math.floor(this.totalContestants / 2);
                            }

                            if (groupCount < 2) {
                                groupCount = 2;
                            }

                            this.$wire.groupForm.numberOfGroups = groupCount;

                            const baseSize = Math.floor(this.totalContestants / groupCount);
                            const extra = this.totalContestants % groupCount;

                            this.groups = Array.from({ length: groupCount }, (_, i) =>
                                i < extra ? baseSize + 1 : baseSize
                            );

                            this.computeContestantsQualifying();
                        },
                        init() {
                            this.calculateGroups();
                        }
                    };
                }
            </script>
        </div>
    @else
        <div>
            <flux:heading size="lg">{{ __('Qualification phase') }}</flux:heading>
            <div class="grid grid-cols-4 gap-1">
                @foreach($tournament->groupPhase->groups as $group)
                    <flux:card class="space-y-6">
                        <flux:heading size="lg">{{ $group->name  }}</flux:heading>
                        <div class="flex flex-col">
                            @foreach($group->getContestants() as $contestant)
                                <div>
                                    {{ $contestant->getName() }}
                                </div>
                            @endforeach
                        </div>
                    </flux:card>
                @endforeach
            </div>
        </div>
    @endif
</div>
