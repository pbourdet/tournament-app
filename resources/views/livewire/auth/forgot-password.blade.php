<flux:card class="w-full sm:max-w-md space-y-6 shadow-md">
    <div>
        <flux:heading size="lg">
            {{ __('Forgot your password?') }}
        </flux:heading>
        <flux:subheading>
            {{ __('Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
        </flux:subheading>
    </div>

    <flux:input label="{{ __('Email') }}" name="email" type="email" autofocus placeholder="{{ __('Your email address') }}"
                wire:model="email" required/>

    <div class="flex justify-center">
        <flux:button variant="primary" icon="envelope" wire:click="sendResetLink">
            {{ __('Email Password Reset Link') }}
        </flux:button>
    </div>
</flux:card>
