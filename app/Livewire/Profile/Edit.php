<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\ToastType;
use App\Livewire\Component;
use App\Livewire\Forms\Profile\UserInformationForm;
use App\Models\User;

class Edit extends Component
{
    public User $user;

    public UserInformationForm $informationForm;

    public function mount(): void
    {
        $this->user = User::findOrFail(auth()->id());
        $this->informationForm->hydrate($this->user);
    }

    public function updateInformation(): void
    {
        $this->informationForm->validate();

        $this->user->username = $this->informationForm->username;
        $this->user->email = $this->informationForm->email;

        if ($this->user->isDirty('email')) {
            $this->user->email_verified_at = null;
        }

        $this->user->save();
        $this->toast(__('Your profile was successfully updated !'), variant: ToastType::SUCCESS->value);
    }

    public function sendVerification(): void
    {
        if ($this->user->hasVerifiedEmail()) {
            $this->toast(__('Your email address is already verified.'), variant: ToastType::DANGER->value);

            return;
        }

        $this->user->sendEmailVerificationNotification();

        $this->toast(__('A new verification link has been sent to the email address you provided during registration.'));
    }
}
