@props(['type' => 'info'])
@php
    $color = match($type) {
        App\Enums\ToastType::SUCCESS => 'green-500',
        App\Enums\ToastType::DANGER => 'red-500',
        App\Enums\ToastType::WARNING => 'yellow-500',
        App\Enums\ToastType::INFO => 'blue-500'
    };

    $icon = match($type) {
        App\Enums\ToastType::SUCCESS => '<path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm13.36-1.814a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" />',
        App\Enums\ToastType::DANGER => '<path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25Zm-1.72 6.97a.75.75 0 1 0-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 1 0 1.06 1.06L12 13.06l1.72 1.72a.75.75 0 1 0 1.06-1.06L13.06 12l1.72-1.72a.75.75 0 1 0-1.06-1.06L12 10.94l-1.72-1.72Z" clip-rule="evenodd" />',
        App\Enums\ToastType::WARNING => '<path fill-rule="evenodd" d="M9.401 3.003c1.155-2 4.043-2 5.197 0l7.355 12.748c1.154 2-.29 4.5-2.599 4.5H4.645c-2.309 0-3.752-2.5-2.598-4.5L9.4 3.003ZM12 8.25a.75.75 0 0 1 .75.75v3.75a.75.75 0 0 1-1.5 0V9a.75.75 0 0 1 .75-.75Zm0 8.25a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />',
        App\Enums\ToastType::INFO => '<path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12Zm8.706-1.442c1.146-.573 2.437.463 2.126 1.706l-.709 2.836.042-.02a.75.75 0 0 1 .67 1.34l-.04.022c-1.147.573-2.438-.463-2.127-1.706l.71-2.836-.042.02a.75.75 0 1 1-.671-1.34l.041-.022ZM12 9a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Z" clip-rule="evenodd" />',
    }
@endphp

<div x-data="toast()" x-init="startTimer()" class="fixed top-3 right-3 w-full max-w-xs sm:max-w-sm md:max-w-md space-y-2">
    <div x-show="isVisible"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 transform translate-x-2"
         x-transition:enter-end="opacity-100 transform translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100 transform translate-x-0"
         x-transition:leave-end="opacity-0 transform translate-x-2"
         class="w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
        <div class="h-1 bg-{{ $color }}" :style="'width: ' + progress + '%'"></div>
        <div class="p-5">
            <div class="flex justify-between items-center">
                <div class="mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-6 h-6 text-{{ $color }}">
                        {!! $icon !!}
                    </svg>
                </div>
                <div>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ session($type->value) }}
                    </p>
                </div>
                <div class="flex">
                    <button @click="isVisible = false" class="p-1 rounded-full text-gray-400 hover:text-gray-900 hover:bg-gray-200 focus:ring-grey-900">
                        <span class="sr-only">Close</span>
                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toast() {
        return {
            isVisible: false,
            progress: 100,
            startTimer() {
                this.$nextTick(() => {
                    this.isVisible = true;
                });
                const interval = setInterval(() => {
                    if (this.progress > 0) {
                        this.progress -= 1;
                    } else {
                        this.isVisible = false;
                        clearInterval(interval);
                    }
                }, 40);
            }
        }
    }
</script>
