<x-slot name="title">{{ __('Reset your password') }} - {{ config('app.name', 'Laravel') }}</x-slot>

<flux:card class="w-full sm:max-w-md space-y-6 shadow-md">
    <div>
        <flux:heading>{{ __('Reset your password') }}</flux:heading>
        <flux:subheading>{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:subheading>
    </div>

    <form class="space-y-6" wire:submit="resetPassword">
        <div class="space-y-4">
            <flux:input label="{{ __('Email') }}" name="email" type="email" autocomplete placeholder="{{ __('Your email address') }}"
                        readonly wire:model="email" variant="filled"/>

            <flux:input autocomplete id="newPassword" type="password" placeholder="{{ __('Your new password') }}" required
                        viewable label="{{ __('New Password') }}" wire:model="passwordForm.password"/>
            <flux:input autocomplete id="newPasswordConfirmation" type="password" placeholder="{{ __('Confirm your new password') }}" required
                        viewable label="{{ __('Confirm Password') }}" wire:model="passwordForm.passwordConfirmation"/>
        </div>

        <flux:button type="submit" variant="primary" class="w-full">{{ __('Reset your password') }}</flux:button>
    </form>
</flux:card>
