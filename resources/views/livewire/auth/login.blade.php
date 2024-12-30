<x-slot name="title">{{ __('Login') }} - {{ config('app.name', 'Laravel') }}</x-slot>

<flux:card class="w-full sm:max-w-md space-y-6 shadow-md">
    <div>
        <flux:heading size="lg">{{ __('Log in to your account') }}</flux:heading>
        <flux:subheading>{{ __('Welcome back !') }}</flux:subheading>
    </div>

    <form class="space-y-6" wire:submit="login">
        <div class="space-y-4">
            <flux:input label="{{ __('Email') }}" name="email" type="email" autofocus placeholder="{{ __('Your email address') }}"
                        wire:model="form.email" required/>

            <flux:field>
                <div class="mb-3 flex justify-between">
                    <flux:label>{{ __('Password') }}</flux:label>

                    <flux:link wire:navigate href="{{ route('password.request') }}" variant="subtle" class="text-sm">
                        {{ __('Forgot your password?') }}
                    </flux:link>
                </div>

                <flux:input viewable name="password" type="password" placeholder="{{ __('Your password') }}"
                            wire:model="form.password" required/>

                <flux:error name="password"/>
            </flux:field>

            <flux:checkbox wire:model="form.rememberMe" label="{{ __('Remember me') }}" />
        </div>

        <div class="space-y-4">
            <flux:button type="submit" variant="primary" class="w-full">{{ __('Log in') }}</flux:button>
            <flux:separator text="{{ __('or') }}"/>
            <flux:button wire:navigate href="{{ route('register') }}" class="w-full">{{ __('Create a new account') }}</flux:button>
        </div>
    </form>
</flux:card>
