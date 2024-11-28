@props(['name', 'checked' => false, 'livewire' => false, 'model' => null])

<x-input-label {{ $attributes }} class="relative inline-flex cursor-pointer items-center">
    <input @if($livewire) wire:model="{{$model}}" @endif type="hidden" name="{{ $name }}" value="0">
    <input @if($livewire) wire:model="{{$model}}" @endif name="{{ $name }}" value="1" type="checkbox" class="peer sr-only" {{ $checked ? 'checked' : '' }} />
    <span id="{{ 'toggle_'.$name }}" class="peer h-6 w-11 rounded-full border bg-slate-200 after:absolute after:left-[2px] after:top-0.5 after:h-5 after:w-5 after:rounded-full after:border after:border-gray-300 after:bg-white after:transition-all after:content-[''] peer-checked:bg-blue-500 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:border-blue-800"></span>
    <span class="ml-1">{{ $slot }}</span>
</x-input-label>
