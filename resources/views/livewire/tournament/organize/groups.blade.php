<div class="w-full space-y-6">
    <flux:tab.group>
        <flux:tabs wire:model="tab">
            <flux:tab dusk="tab-settings" icon="cog-6-tooth" name="settings">{{ __('Settings') }}</flux:tab>
            <flux:tab dusk="tab-groups" icon="rectangle-group" name="groups">{{ __('Groups') }}</flux:tab>
        </flux:tabs>

        <flux:tab.panel class="space-y-6" name="settings">
            <div>
                <flux:heading size="lg">{{ __('Set up the group phase') }}</flux:heading>
                <flux:subheading>{{ __('Custom the groups to your needs.') }}</flux:subheading>
            </div>

            <div class="space-y-6">
                <flux:subheading class="flex items-center">
                    <flux:icon.information-circle class="mr-2"/>
                    {{ __('Contestants are divided into groups and compete in a round-robin format, earning points based on performance. The top teams from each group advance to elimination phase. Examples include the FIFA World Cup, UEFA Champions League, and ICC Cricket World Cup.') }}
                </flux:subheading>
                <flux:card class="shadow-xs space-y-6" x-data="groupPhase({{ $tournament->contestantsCount() }})">
                    <div class="space-y-4">
                        <div class="flex max-sm:flex-col">
                            <flux:input class="w-20! max-sm:mb-4" label="{{ __('Number of groups') }}" type="number"
                                        wire:model="form.numberOfGroups" min="2" :max="$tournament->contestantsCount() / 2"
                                        @input="calculateGroups()" dusk="input-number-of-groups"/>
                            <flux:separator vertical class="mx-3"/>
                            <flux:input class="w-20!" label="{{ __('Contestants qualifying per group') }}" type="number"
                                        wire:model="form.contestantsQualifying"
                                        @input="computeContestantsQualifying()"
                                        min="1" x-bind:max="Math.min(...groups)" dusk="input-contestants-qualifying"/>
                        </div>
                        <div>
                            <p>{{ __('Total contestants qualifying') }} : <span x-text="groups.length * $wire.form.contestantsQualifying"></span></p>
                            <p>{{ __('Total matches') }} : <span x-text="computeMatches()"></span></p>
                        </div>
                        <div class="grid max-sm:grid-cols-2 grid-cols-4 gap-1 mt-3">
                            <template x-for="(group, index) in groups" :key="index">
                                <div class="px-3 py-1 border-zinc-200 dark:border-zinc-900 rounded-lg bg-zinc-50 dark:bg-zinc-700">
                                    <h3 class="font-bold text-lg">{{ __('Group') }} <span x-text="index + 1"></span></h3>
                                    <div>
                                        <template x-for="(contestant, contestantIndex) in group" :key="contestantIndex">
                                            <div>
                                                <flux:badge x-show="contestantIndex >= $wire.form.contestantsQualifying" icon-trailing="{{ $tournament->team_based ? 'users' : 'user' }}"
                                                            variant="pill" class="mt-1 opacity-50">
                                                    <span x-text="@js($tournament->team_based) ? 'T' + contestant : 'P' + contestant"></span>
                                                </flux:badge>
                                                <flux:badge x-show="contestantIndex < $wire.form.contestantsQualifying" icon-trailing="{{ $tournament->team_based ? 'users' : 'user' }}"
                                                            color="lime" variant="pill" class="mt-1">
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
                                if (this.$wire.form.contestantsQualifying > Math.min(...this.groups)) {
                                    this.$wire.form.contestantsQualifying = Math.min(...this.groups);
                                }
                            },
                            calculateGroups() {
                                let groupCount = this.$wire.form.numberOfGroups;

                                if (groupCount > this.totalContestants / 2) {
                                    groupCount = Math.floor(this.totalContestants / 2);
                                }

                                if (groupCount < 2) {
                                    groupCount = 2;
                                }

                                this.$wire.form.numberOfGroups = groupCount;

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
        </flux:tab.panel>
        <flux:tab.panel class="space-y-6" name="groups">
            <div>
                <flux:button dusk="generate-groups"  wire:click="generateGroups" icon="arrow-path"
                             :disabled="!$tournament->groupPhase?->canGenerateGroups()">
                    {{ __('Random groups') }}
                </flux:button>
            </div>
            @include('livewire.tournament.partials.groups-grid')
        </flux:tab.panel>
    </flux:tab.group>
</div>
