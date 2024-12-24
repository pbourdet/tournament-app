<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Enums\ToastType;
use App\Livewire\Component;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;

class SendVerificationEmail extends Component
{
    use WithRateLimiting;

    public string $text = '';

    public function sendVerification(): void
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException) {
            $this->toast(__('Slow down ! You can try again later.'), variant: ToastType::DANGER->value);

            return;
        }

        $user = User::findOrFail(auth()->id());

        if ($user->hasVerifiedEmail()) {
            $this->toast(__('Your email address is already verified.'), variant: ToastType::DANGER->value);

            return;
        }

        $user->sendEmailVerificationNotification();

        $this->toast(__('A new verification link has been sent to the email address you provided during registration.'));
    }
}
