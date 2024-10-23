@props(['defaultTab'])

<div x-data="{activeTab: '{{ $defaultTab }}'}">
    <div>
        <ul class="flex flex-wrap text-md font-medium text-center text-gray-500 border-b border-gray-200">
            {{ $tabs }}
        </ul>
    </div>

    <div>
        {{ $tabContents }}
    </div>
</div>
