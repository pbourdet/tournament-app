<div>
    <h3 class="p-8 text-lg leading-6 font-bold">
        {{ $title }}
    </h3>
    <div class="mt-2">
        @foreach ($tournaments as $tournament)
            @can('manage', $tournament)
                <a href="{{ route('tournaments.organize', ['tournament' => $tournament, 'page' => 'general']) }}" wire:navigate class="block border rounded-lg dark:border-zinc-700 dark:hover:bg-zinc-700 border-zinc-200 hover:bg-zinc-200 hover:shadow-md transition duration-150">
            @else
                <a href="{{ route('tournaments.show', ['tournament' => $tournament, 'page' => 'overview']) }}" wire:navigate class="block border rounded-lg border-zinc-200 hover:bg-zinc-200 dark:border-zinc-700 dark:hover:bg-zinc-700 hover:shadow-md transition duration-150">
            @endcan
                <div class="p-8">
                    <h4 class="text-lg font-bold text-accent-content mb-2">{{ $tournament->name }}</h4>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $tournament->description }}</p>
                </div>
            </a>
        @endforeach
    </div>
</div>
