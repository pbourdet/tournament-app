<div {{ $attributes->merge(['class' => 'max-w-7xl mx-auto sm:px-6 lg:px-8']) }}>
    <div class="h-auto min-h-0 max-h-screen shadow-md sm:rounded-lg">
        {{ $slot }}
    </div>
</div>
