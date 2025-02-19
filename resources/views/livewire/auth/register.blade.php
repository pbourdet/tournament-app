<x-slot name="title">{{ __('Register') }} - {{ config('app.name', 'Laravel') }}</x-slot>

<flux:card class="w-full sm:max-w-lg space-y-6 shadow-md overflow-y-hidden">
    <div>
        <flux:heading size="lg">{{ __('Create a new account') }}</flux:heading>
        <flux:subheading>{{ __('Welcome !') }}</flux:subheading>
    </div>

    <form x-data="withPreview()" class="space-y-6" wire:submit="register">
        <div class="space-y-4">
            <flux:field>
                <flux:label>{{ __('Profile picture') }}</flux:label>
                <div class="flex flex-col items-center space-y-2">
                    <div class="relative">
                        <img x-ref="picturePreview"
                             src="{{ $form->profilePicture?->temporaryUrl() ?? Storage::url('user-picture-placeholder.jpeg') }}"
                             alt="{{ __('Profile picture') }}" class="size-12 rounded-full object-cover">
                        <flux:button size="sm" icon="x-circle" x-cloak variant="subtle"
                                     class="absolute! bottom-7 left-10 rounded-full!"
                                     x-show="$wire.form.profilePicture !== null" @click="removePreview()"/>
                        <x-loader wire:loading wire:target="form.profilePicture" class="absolute! bottom-2 right-16 size-8"/>
                    </div>
                    <flux:input accept="image/*" type="file" wire:model="form.profilePicture" />
                </div>
                <flux:error name="form.profilePicture" />
            </flux:field>
            <flux:input id="email" type="email" label="{{ __('Email') }}" wire:model="form.email" placeholder="{{ __('Your email address') }}" required/>
            <flux:input id="username" label="{{ __('Username') }}" wire:model="form.username" placeholder="{{ __('Your username') }}" required/>
            <flux:input id="password" viewable type="password" label="{{ __('Password') }}" wire:model="form.password" placeholder="{{ __('Your password') }}" required/>
            <flux:input id="passwordConfirmation" viewable type="password" label="{{ __('Confirm Password') }}" wire:model="form.passwordConfirmation" placeholder="{{ __('Confirm Password') }}" required/>
        </div>

        <div class="space-y-4">
            <flux:button type="submit" variant="primary" class="w-full" dusk="register-button">
                {{ __('Create a new account') }}
            </flux:button>
            <flux:separator text="{{ __('or') }}"/>
            <flux:button href="{{ route('login') }}" class="w-full">{{ __('Log in') }}</flux:button>
        </div>
    </form>

    <script>
        function withPreview() {
            return {
                removePreview() {
                    this.$refs.picturePreview.src = '{{ Storage::url('user-picture-placeholder.jpeg') }}';
                    this.$wire.form.profilePicture = null;
                },
            }
        }
    </script>
</flux:card>
