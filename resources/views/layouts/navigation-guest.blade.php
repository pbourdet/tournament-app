<flux:header container class="bg-gray-50">
    <flux:brand wire:navigate class="hidden sm:flex" href="{{ route('dashboard') }}" logo="{{ Vite::asset('resources/images/app-logo.png')  }}" name="{{ config('app.name', 'Laravel') }}"/>
    <flux:brand wire:navigate class="sm:hidden" href="{{ route('dashboard') }}" logo="{{ Vite::asset('resources/images/app-logo.png')  }}"/>

    <flux:spacer/>
    <flux:navbar>
        <flux:button class="mr-2" variant="primary" wire:navigate href="{{ route('register') }}">
            <flux:icon.user-plus class="size-5 hidden sm:block" />
            {{ __('Register') }}
        </flux:button>
        <flux:button wire:navigate href="{{ route('login') }}">
            <flux:icon.arrow-right-end-on-rectangle class="size-5 hidden sm:block" />
            {{ __('Log in') }}
        </flux:button>
    </flux:navbar>
</flux:header>
