<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Enums\ToastType;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class EmailVerificationNotificationController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = $request->user();

        if ($user->hasVerifiedEmail()) {
            return redirect()->intended(route('dashboard', absolute: false));
        }

        $user->sendEmailVerificationNotification();

        return back()->with(ToastType::INFO->value, __('A new verification link has been sent to the email address you provided during registration.'));
    }
}
