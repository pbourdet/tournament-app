<flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <flux:brand href="#" logo="{{ Vite::asset('resources/images/app-logo.png')  }}" name="MyTournaments"
                class="px-2 dark:hidden"/>
    <flux:brand href="#" logo="{{ Vite::asset('resources/images/app-logo.png')  }}" name="MyTournaments"
                class="px-2 hidden dark:flex"/>

    <flux:modal.trigger name="new-tournament">
        <flux:button variant="primary" class="w-full" icon="plus-circle" dusk="new-tournament">
            {{ __('New tournament') }}
        </flux:button>
    </flux:modal.trigger>

    <flux:navlist variant="outline">
        <flux:navlist.item wire:navigate icon="home" href="{{ route('dashboard') }}">{{ __('Dashboard') }}</flux:navlist.item>
    </flux:navlist>
    <flux:spacer/>

    <flux:dropdown position="top" align="start" class="max-lg:hidden">
        <flux:profile avatar="{{ Storage::url(Auth::user()->getProfilePicture()) }}" name="{{ Auth::user()->username }}"/>

        <flux:menu>
            <flux:menu.item href="{{ route('profile.edit') }}" icon="user">Profile</flux:menu.item>
            <flux:menu.separator/>

            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <flux:menu.item onclick="event.preventDefault(); this.closest('form').submit();"
                                icon="arrow-right-start-on-rectangle">
                    {{ __('Log Out') }}
                </flux:menu.item>
            </form>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<flux:header class="lg:!hidden !block bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
    <flux:navbar class="lg:hidden w-full">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left"/>

        <flux:modal.trigger name="new-tournament">
            <flux:button class="ml-4" variant="primary" icon="plus-circle">
                {{ __('New tournament') }}
            </flux:button>
        </flux:modal.trigger>

        <flux:spacer/>

        <flux:dropdown position="top" align="start">
            <flux:profile avatar="{{ Storage::url(Auth::user()->getProfilePicture()) }}"/>

            <flux:menu>
                <flux:menu.item href="{{ route('profile.edit') }}" icon="user">Profile</flux:menu.item>

                <flux:menu.separator/>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <flux:menu.item onclick="event.preventDefault(); this.closest('form').submit();"
                                    icon="arrow-right-start-on-rectangle">
                        {{ __('Log Out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:navbar>
</flux:header>

<livewire:tournament.join/>
