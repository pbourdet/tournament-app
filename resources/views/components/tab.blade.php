@props(['tabName'])

<li>
    <a href="#"
       id="tab-{{ $tabName }}"
       :class="activeTab === '{{ $tabName }}' ? 'font-semibold text-cyan-700 bg-gray-100' : 'hover:text-gray-600 hover:bg-gray-50'"
       class="inline-block p-4 rounded-t-lg"
       @click.prevent="switchTab('{{ $tabName }}')">
        {{ __(ucfirst($tabName)) }}
    </a>
</li>
