<nav x-data="{ open: false }" class="rounded-md bg-white border-b border-gray-200">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="h-16">
            <div class="flex justify-between h-full items-center">
                <div class="shrink-0 flex items-center">
                    <a href="/">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800"/>
                    </a>
                </div>

                <div class="h-full flex items-center">
                    <a class="mr-2" href="{{ route('register') }}">
                        <x-primary-button type="button">
                            {{ __('Register') }}
                        </x-primary-button>
                    </a>

                    <a href="{{ route('login') }}">
                        <x-secondary-button type="button">
                            {{ __('Log in') }}
                        </x-secondary-button>
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
