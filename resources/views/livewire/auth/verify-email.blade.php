<x-slot name="title">{{ __('Verify email address') }} - {{ config('app.name', 'Laravel') }}</x-slot>

<flux:main>
    <flux:card class="max-w-xl space-y-4 shadow-md">
        <div>
            <flux:heading size="lg">{{ __('Thanks for signing up !') }}</flux:heading>
            <flux:subheading>
                {{ __("Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.") }}
            </flux:subheading>
        </div>

        <livewire:auth.send-verification-email :text="__('Resend verification email')"/>
    </flux:card>
</flux:main>
