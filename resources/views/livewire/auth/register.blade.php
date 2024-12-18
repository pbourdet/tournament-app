<flux:card class="w-full sm:max-w-md space-y-6 shadow-md">
    <div>
        <flux:heading size="lg">{{ __('Log in to your account') }}</flux:heading>
        <flux:subheading>{{ __('Welcome back !') }}</flux:subheading>
    </div>

    <form class="space-y-6" wire:submit="register">
        <div class="space-y-4">
            <flux:input wire:model="form.profilePicture" type="file"/>
            <flux:field>
                <flux:label>{{ __('Email') }}</flux:label>
                <flux:input wire:model="form.email" name="email" type="email" autofocus placeholder="{{ __('Your email address') }}" required />
                <flux:error name="form.email" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Username') }}</flux:label>
                <flux:input wire:model="form.username" name="username" required placeholder="{{ __('Your username') }}" />
                <flux:error name="form.username" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Password') }}</flux:label>
                <flux:input wire:model="form.password" name="password" type="password" placeholder="{{ __('Your password') }}" required />
                <flux:error name="form.password" />
            </flux:field>
            <flux:field>
                <flux:label>{{ __('Confirm Password') }}</flux:label>
                <flux:input wire:model="form.passwordConfirmation" name="passwordConfirmation" type="password" placeholder="{{ __('Confirm your password') }}" required />
                <flux:error name="form.passwordConfirmation" />
            </flux:field>
        </div>

        <div class="space-y-4">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Create a new account') }}
            </flux:button>
            <flux:separator text="{{ __('or') }}"/>
            <flux:button href="{{ route('login') }}" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>
</flux:card>
