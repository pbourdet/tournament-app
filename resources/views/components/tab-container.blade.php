@props(['defaultTab'])

<div x-cloak x-data="tabContainer('{{ $defaultTab }}')">
    <div>
        <ul class="flex flex-wrap text-md font-medium text-center text-gray-500 border-b border-gray-200">
            {{ $tabs }}
        </ul>
    </div>

    <div>
        {{ $tabContents }}
    </div>
</div>

<script>
    function tabContainer(defaultTab) {
        return {
            activeTab: new URLSearchParams(window.location.search).get('tab') || defaultTab,

            init() {
                window.addEventListener('popstate', () => {
                    this.activeTab = new URLSearchParams(window.location.search).get('tab') || defaultTab;
                });
            },

            switchTab(tabName) {
                this.activeTab = tabName;
                const url = new URL(window.location);
                url.searchParams.set('tab', tabName);
                window.history.pushState({}, '', url);
            }
        }
    }
</script>
