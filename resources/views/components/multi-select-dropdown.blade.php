@props(['options' => '', 'selectionsCount' => 0, 'placeholder' => __('Select'), 'resetEvent' => null])

<div class="w-full relative" x-data="dropdown()" x-init="refreshOptions()">
    <div id="dynamic-options" data-options=@json($options) class="hidden"></div>
    <div
        x-ref="badgesContainer"
        @click="open = !open"
        class="p-1 rounded-md flex gap-2 text-sm flex-wrap border border-gray-300 cursor-pointer truncate bg-white relative overflow-x-auto items-center min-h-10"
    >
        <span x-show="selections.length === 0" class="text-gray-500">{{ $placeholder }}</span>
        <template x-for="badge in selections" :key="badge.id">
            <div class="bg-cyan-600 text-white px-2 py-1 rounded-lg gap-1">
                <div class="flex items-center">
                    <span class="align-middle" x-text="badge.name"></span>
                    <button @click.stop="toggleOption(badge.id)" class="text-white hover:text-gray-300 ml-1">&times;</button>
                </div>
            </div>
        </template>
    </div>

    <div
        class="pr-3 pl-3 pt-1 pb-3 rounded-lg flex gap-3 shadow-lg absolute flex-col bg-white max-h-64 overflow-y-auto z-10"
        x-show="open"
        x-trap="open"
        @click.outside="open = false"
        @keydown.escape.window="open = false"
        x-transition:enter="ease-[cubic-bezier(.3,2.3,.6,1)] duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-out duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0">
        <x-text-input x-model="filter" placeholder="{{ __('Filter') }}"/>

        <template x-for="(name, id) in filteredOptions" :key="id">
            <div
                @click="toggleOption(id, name)"
                class="flex items-center justify-between p-2 rounded-lg hover:bg-gray-200"
                :class="{
                    'bg-blue-100': selections.some(sel => sel.id === id),
                    'opacity-50 cursor-not-allowed': hasMaxSelection && !selections.some(sel => sel.id === id)
                }"
                :disabled="hasMaxSelection && !selections.some(sel => sel.id === id)">
                <span x-text="name" class="text-sm font-medium text-gray-900"></span>
                <span x-show="selections.some(sel => sel.id === id)" class="font-bold text-sm">✓</span>
            </div>
        </template>

        <p x-show="Object.keys(filteredOptions).length === 0"
           class="text-neutral-500 pt-2 text-center border-t">
            {{ __('No results') }}
        </p>
    </div>

    <script>
        function dropdown() {
            return {
                resetEvent: '{{ $resetEvent ?? null }}',
                selections: [],
                open: false,
                filter: '',
                options: {},
                maxSelections: {{ $selectionsCount }},
                get hasMaxSelection() {
                    return this.maxSelections > 0 && this.selections.length >= this.maxSelections;
                },
                toggleOption(id, name) {
                    const existing = this.selections.find(sel => sel.id === id);
                    if (existing) {
                        this.selections = this.selections.filter(sel => sel.id !== id);
                        this.$dispatch('selection-removed', { id });
                    } else if (!this.hasMaxSelection) {
                        this.selections.push({ id, name });
                        this.$dispatch('selection-added', { id });
                        if (this.selections.length === this.maxSelections && this.maxSelections > 0) {
                            this.open = false;
                        }
                    }
                },
                get filteredOptions() {
                    return Object.fromEntries(
                        Object.entries(this.options).filter(([id, name]) =>
                            name.toLowerCase().includes(this.filter.toLowerCase())
                        )
                    );
                },
                refreshOptions() {
                    const newOptions = document.getElementById('dynamic-options').getAttribute('data-options');
                    this.options = JSON.parse(newOptions);
                },
                init() {
                    if (this.resetEvent !== '') {
                        window.addEventListener(this.resetEvent, () => {
                            this.reset();
                        });
                    }

                    const observer = new MutationObserver(() => {
                        this.refreshOptions();
                    });

                    const target = document.getElementById('dynamic-options');
                    observer.observe(target, { attributes: true, attributeFilter: ['data-options'] });
                },
                reset() {
                    this.selections = [];
                    this.filter = '';
                    this.open = false;
                }
            }
        }
    </script>
</div>
