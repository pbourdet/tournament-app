<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800"/>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>

                <div class="mx-4 m-auto">
                    <x-primary-button type="button" x-on:click.prevent="$dispatch('open-modal', 'new-tournament')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="mr-1 w-5 h-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                  d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
                        </svg>
                        {{ __('New tournament') }}
                    </x-primary-button>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div id="dropdown" class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div class="flex items-center">
                                <img src="{{ Storage::url(Auth::user()->getProfilePicture()) }}"
                                     class="w-8 h-8 rounded-full object-cover mr-2">
                                {{ Auth::user()->name }}
                            </div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg"
                                     viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                          d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                          clip-rule="evenodd"/>
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                             onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open"
                        class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                              stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 6h16M4 12h16M4 18h16"/>
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                              stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <x-modal name="new-tournament" focusable>
        <div class="p-6">
            <div x-data="codeInput()">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg text-gray-900">{{ __('Join a tournament') }}</h2>
                    <div @click="$dispatch('close')"
                         class="cursor-pointer text-gray-500 hover:bg-gray-200 hover:text-gray-900 rounded-md w-8 h-8 inline-flex justify-center items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                             stroke="currentColor" class="w-6 h-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                        </svg>
                        <span class="sr-only">Close modal</span>
                    </div>
                </div>
                <p class="mt-1 text-sm text-gray-600">{{ __('Enter the tournament invitation code') }}</p>
                <div class="mt-6 flex justify-center items-center">
                    <x-text-input type="text" placeholder="{{ Str::random(6) }}..." maxlength="6"
                                  class="w-40 text-center text-2xl uppercase"
                                  x-model="code"
                                  name="invitation-code"
                                  @input="checkCode()"
                    />
                </div>
                <div class="flex justify-center mt-5">
                    <x-loader id="loader" class="htmx-indicator w-8 h-8 text-gray-200 animate-spin fill-blue-600"/>
                </div>
                <button class="hidden" hx-trigger="click delay:300ms" hx-target="#invitation-response"
                        hx-indicator="#loader" x-ref="invitationLink"></button>
                <div class="w-100" id="invitation-response"></div>
            </div>

            <script>
                function codeInput() {
                    return {
                        code: '',
                        checkCode() {
                            if (this.code.length === 6) {
                                this.$nextTick(() => {
                                    var $button = this.$refs.invitationLink
                                    $button.setAttribute('hx-get', `/invitations/${this.code}/join`);
                                    htmx.process($button)
                                    $button.click();
                                });
                            }
                        }
                    }
                }
            </script>

            <x-divider>{{ __('or') }}</x-divider>

            <div class="text-center">
                <a href="{{ route('tournaments.create') }}">
                    <x-primary-button :disabled="Auth::user()->cannot('create', App\Models\Tournament::class)" type="button">
                        <span class="text-lg">{{ __('Create a tournament') }}</span>
                    </x-primary-button>
                    @cannot('create', App\Models\Tournament::class)
                        <x-input-error class="mt-1" :messages="__('You cannot create more tournaments')" />
                    @endcannot
                </a>
            </div>
        </div>
    </x-modal>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="flex item-center">
                    <img src="{{ Storage::url(Auth::user()->getProfilePicture()) }}"
                         class="w-8 h-8 rounded-full object-cover mr-2">
                    <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                </div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                                           onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
