<div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-md sm:rounded-lg">
        <div class="bg-white border-b border-gray-200">
            <h3 class="p-8 text-lg leading-6 text-gray-900 font-bold">
                {{ $title }}
            </h3>
            <div class="mt-2">
                @foreach ($tournaments as $tournament)
                    <a href="#" class="block border rounded-lg border-gray-200 hover:bg-gray-200 hover:shadow-md transition duration-150">
                        <div class="p-8 hover:bg-gray-100">
                            <h4 class="text-lg font-bold text-cyan-700 mb-2">{{ $tournament->name }}</h4>
                            <p class="text-sm text-gray-500">{{ $tournament->description }}</p>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>
