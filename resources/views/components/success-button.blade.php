<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-2 py-2 bg-emerald-500 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-600 active:bg-emerald-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-25']) }}>
    {{ $slot }}
</button>
