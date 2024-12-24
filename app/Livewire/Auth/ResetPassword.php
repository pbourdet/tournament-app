<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Enums\ToastType;
use App\Livewire\Component;
use App\Livewire\Forms\Profile\PasswordForm;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;

#[Layout('layouts.guest')]
class ResetPassword extends Component
{
    public string $token = '';

    #[Url]
    public string $email = '';

    public PasswordForm $passwordForm;

    public function resetPassword(): void
    {
        $this->passwordForm->validateOnly('password');

        $status = Password::reset(
            ['email' => $this->email, 'token' => $this->token, 'password' => $this->passwordForm->password, 'password_confirmation' => $this->passwordForm->passwordConfirmation],
            function (User $user) {
                $user->forceFill([
                    'password' => Hash::make($this->passwordForm->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        if (Password::PASSWORD_RESET !== $status) {
            $this->toast(__('Password reset failed. Try again or request a reset new link.'), variant: ToastType::DANGER->value);

            return;
        }

        $this->toast('Your password has been reset ! You can now log in with your new password.', variant: ToastType::SUCCESS->value);
        $this->redirectRoute('login', navigate: true);
    }
}
