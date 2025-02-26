<flux:sidebar sticky stashable class="bg-zinc-50 dark:bg-zinc-900 border-r border-zinc-200 dark:border-zinc-700">
    <flux:sidebar.toggle class="lg:hidden" icon="x-mark"/>

    <flux:brand wire:navigate href="{{ route('dashboard') }}" logo="{{ Vite::asset('resources/images/app-logo.png')  }}" name="{{ config('app.name', 'Laravel') }}"
                class="px-2 dark:hidden"/>
    <flux:brand wire:navigate href="{{ route('dashboard') }}" logo="{{ Vite::asset('resources/images/app-logo.png')  }}" name="{{ config('app.name', 'Laravel') }}"
                class="px-2 hidden dark:flex"/>

    <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
        <flux:radio value="light" icon="sun"/>
        <flux:radio value="dark" icon="moon"/>
    </flux:radio.group>
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
            <flux:menu.item wire:navigate href="{{ route('profile.edit') }}" icon="user">Profile</flux:menu.item>
            <flux:menu.separator/>

            <livewire:auth.logout/>
        </flux:menu>
    </flux:dropdown>
</flux:sidebar>

<flux:header class="lg:hidden! block! bg-white lg:bg-zinc-50 dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-700">
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
                <flux:menu.item wire:navigate href="{{ route('profile.edit') }}" icon="user">Profile</flux:menu.item>

                <flux:menu.separator/>

                <livewire:auth.logout/>
            </flux:menu>
        </flux:dropdown>
    </flux:navbar>
</flux:header>

<livewire:tournament.join/>
