@props(['tabName'])

<li>
    <a href="#"
       :class="activeTab === '{{ $tabName }}' ? 'font-semibold text-cyan-700 bg-gray-100' : 'hover:text-gray-600 hover:bg-gray-50'"
       class="inline-block p-4 rounded-t-lg"
       @click.prevent="activeTab = '{{ $tabName }}'">
        {{ ucfirst($tabName) }}
    </a>
</li>
