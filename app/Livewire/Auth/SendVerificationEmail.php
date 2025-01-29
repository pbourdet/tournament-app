<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Component;
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
            $this->toastError(__('Slow down ! You can try again later.'));

            return;
        }

        $user = $this->getUser();

        if ($user->hasVerifiedEmail()) {
            $this->toastError(__('Your email address is already verified.'));

            return;
        }

        $user->sendEmailVerificationNotification();

        $this->toast(__('A new verification link has been sent to the email address you provided during registration.'));
    }
}
