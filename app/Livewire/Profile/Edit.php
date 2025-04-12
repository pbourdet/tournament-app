<?php

declare(strict_types=1);

namespace App\Livewire\Profile;

use App\Enums\SupportedLocale;
use App\Livewire\Component;
use App\Livewire\Forms\Profile\PasswordForm;
use App\Livewire\Forms\Profile\UserDeletionForm;
use App\Livewire\Forms\Profile\UserInformationForm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;

class Edit extends Component
{
    public UserInformationForm $informationForm;

    public PasswordForm $passwordForm;

    public UserDeletionForm $deletionForm;

    #[Validate(new Enum(SupportedLocale::class))]
    public string $language;

    public function mount(): void
    {
        $this->informationForm->hydrate($this->user);
        $this->language = $this->user->language;
    }

    public function updatedLanguage(): void
    {
        $this->validateOnly('language');

        $this->user->update(['language' => $this->language]);

        app()->setLocale($this->language);

        $this->toastSuccess(__('Your language preference has been updated.'));
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
        $this->toastSuccess(__('Your profile was successfully updated !'));
    }

    public function updatePassword(): void
    {
        try {
            $this->passwordForm->validate();

            $this->user->update(['password' => Hash::make($this->passwordForm->password)]);
            $this->toastSuccess(__('Your password was successfully updated !'));
        } finally {
            $this->passwordForm->reset();
        }
    }

    public function deleteAccount(): void
    {
        $this->deletionForm->validate();
        $user = $this->user;

        Auth::logout();

        $user->delete();

        session()->invalidate();
        session()->regenerateToken();

        $this->toastSuccess(__('Your account has been deleted.'));
        $this->redirectRoute('login', navigate: true);
    }
}
