<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Component;
use App\Livewire\Forms\Auth\LoginForm;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')]
class Login extends Component
{
    public LoginForm $form;

    private Request $request;

    public function login(Request $request): void
    {
        $this->request = $request;

        $this->form->validate();
        $this->authenticate();
        session()->regenerate();
        $this->redirectIntended(route('dashboard'), navigate: true);
    }

    /** @throws ValidationException */
    private function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (!Auth::attempt($this->form->getCredentials(), $this->form->rememberMe)) {
            RateLimiter::hit($this->throttleKey());

            $this->form->reset('password');
            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /** @throws ValidationException */
    private function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout($this->request));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        $this->form->reset('password');
        throw ValidationException::withMessages(['email' => __('auth.throttle', ['seconds' => $seconds])]);
    }

    private function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->form->email).'|'.$this->request->ip());
    }
}
