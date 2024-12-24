<flux:main>
    <flux:card class="max-w-xl space-y-4 shadow-md">
        <div>
            <flux:heading size="lg">Thanks for signing up!</flux:heading>
            <flux:subheading>
                {{ __('Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
            </flux:subheading>
        </div>

        <div>
            <livewire:auth.send-verification-email :text="__('Resend verification email')"/>
            <form method="POST" action="{{ route('logout') }}">
                @csrf

                <button type="submit" class="underline text-xs text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    {{ __('Log Out') }}
                </button>
            </form>
        </div>
    </flux:card>
</flux:main>
