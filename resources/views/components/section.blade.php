<div {{ $attributes->merge(['class' => 'max-w-7xl mx-auto sm:px-6 lg:px-8']) }}>
    <div class="bg-white shadow-md sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
