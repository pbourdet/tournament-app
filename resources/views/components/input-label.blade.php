@props(['value', 'mandatory' => null])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-gray-700']) }}>
    {{ $value ?? $slot }}
    @if(true === $mandatory && $value)
        <span class="text-red-500"> *</span>
    @endif
    @if(false === $mandatory && $value)
        <span class="text-gray-400">({{ __('Optional') }})</span>
    @endif
</label>
