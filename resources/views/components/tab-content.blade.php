@props(['tabName'])

<div x-show="activeTab === '{{ $tabName }}'"
     x-transition:enter="transition-opacity ease-out duration-400"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     class="p-6 bg-white border rounded shadow">
    {{ $slot }}
</div>
