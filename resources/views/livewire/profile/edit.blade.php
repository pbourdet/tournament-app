<x-slot name="title">{{ __('Profile') }} - {{ config('app.name', 'Laravel') }}</x-slot>

<flux:main class="max-w-3xl space-y-6">
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
                        <livewire:auth.send-verification-email :text="__('Click here to re-send the verification email.')"/>
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
            <flux:heading>{{ __('Language') }}</flux:heading>
            <flux:subheading>{{ __('Set your preferred language') }}</flux:subheading>
        </div>

        <div>
            <div class="flex items-center space-x-2">
                <div class="w-1/3">
                    <flux:select wire:model.live="language" variant="listbox">
                        @foreach(\App\Enums\SupportedLocale::cases() as $locale)
                            <flux:select.option value="{{ $locale->value }}">
                                {{ $locale->getLabel() }}
                            </flux:select.option>
                        @endforeach
                    </flux:select>
                </div>
                <flux:icon.loading wire:loading wire:target="language"/>
            </div>
            <flux:error name="language" />
        </div>
    </flux:card>

    <flux:card class="space-y-6 shadow-md">
        <div>
            <flux:heading>{{ __('Update Password') }}</flux:heading>
            <flux:subheading>{{ __('Ensure your account is using a long, random password to stay secure.') }}</flux:subheading>
        </div>
        <form wire:submit="updatePassword" class="space-y-4">
            <flux:input autocomplete id="currentPassword" type="password" placeholder="{{ __('Your current password') }}" required
                         viewable label="{{ __('Current Password') }}" wire:model="passwordForm.currentPassword" />
            <flux:input autocomplete id="newPassword" type="password" placeholder="{{ __('Your new password') }}" required
                        viewable label="{{ __('New Password') }}" wire:model="passwordForm.password"/>
            <flux:input autocomplete id="newPasswordConfirmation" type="password" placeholder="{{ __('Confirm your new password') }}" required
                        viewable label="{{ __('Confirm Password') }}" wire:model="passwordForm.passwordConfirmation"/>
            <div>
                <flux:button type="submit" variant="primary">{{ __('Save') }}</flux:button>
            </div>
        </form>
    </flux:card>

    <flux:card class="space-y-6 shadow-md">
        <div>
            <flux:heading>{{ __('Delete Account') }}</flux:heading>
            <flux:subheading>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}</flux:subheading>
        </div>
        <div>
            <flux:modal.trigger name="delete-account">
                <flux:button variant="danger">{{ __('Delete Account') }}</flux:button>
            </flux:modal.trigger>
        </div>
    </flux:card>
    <flux:modal name="delete-account" class="min-w-[22rem] space-y-6">
        <div>
            <flux:heading>{{ __('Are you sure you want to delete your account?') }}</flux:heading>
            <flux:subheading>{{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}</flux:subheading>
        </div>
        <form wire:submit="deleteAccount" class="space-y-4">
            <flux:input autocomplete id="password" type="password" placeholder="{{ __('Your password') }}" required
                        viewable label="{{ __('Password') }}" wire:model="deletionForm.password"/>
            <div class="flex gap-2">
                <flux:spacer/>
                <flux:modal.close>
                    <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                </flux:modal.close>
                <flux:button type="submit" variant="danger">{{ __('Delete Account') }}</flux:button>
            </div>
        </form>
    </flux:modal>
</flux:main>
