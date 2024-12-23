<flux:main class="max-w-xl space-y-6">
    <flux:card class="space-y-6 shadow-md">
        <div>
            <flux:heading>{{ __('Profile Information') }}</flux:heading>
            <flux:subheading>{{ __("Update your account's profile information and email address.") }}</flux:subheading>
        </div>
        <form wire:submit="updateInformation" class="space-y-4">
            <flux:input autocomplete id="username" label="{{ __('Username') }}" wire:model="informationForm.username" placeholder="{{ __('Your username') }}" required/>
            <flux:input autocomplete id="email" type="email" label="{{ __('Email') }}" wire:model="informationForm.email" placeholder="{{ __('Your email address') }}" required/>
            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && !$user->hasVerifiedEmail())
                <div>
                    <p class="text-xs mt-2 text-gray-800">
                        {{ __('Your email address is unverified.') }}

                        <button type="button" wire:click="sendVerification"
                            class="underline text-xs text-accent hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent">
                            {{ __('Click here to re-send the verification email.') }}
                        </button>
                    </p>
                </div>
            @endif
            <div>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
                <p class="text-sm text-gray-500" wire:dirty wire:target="informationForm">{{ __('Unsaved changes...') }}</p>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-6 shadow-md">
        <div>
            <flux:heading>{{ __('Update Password') }}</flux:heading>
            <flux:subheading>{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:subheading>
        </div>
        <form wire:submit="updatePassword" class="space-y-4">
            <flux:input autocomplete id="currentPassword" type="password" placeholder="{{ __('Your current password') }}" required
                         viewable label="{{ __('Current Password') }}" wire:model="passwordForm.currentPassword" />
            <flux:input autocomplete id="password" type="password" placeholder="{{ __('Your new password') }}" required
                        viewable label="{{ __('New Password') }}" wire:model="passwordForm.password"/>
            <flux:input autocomplete id="passwordConfirmation" type="password" placeholder="{{ __('Confirm your new password') }}" required
                        viewable label="{{ __('Confirm Password') }}" wire:model="passwordForm.passwordConfirmation"/>
            <div>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:card>
</flux:main>
