<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Livewire\Component;
use App\Livewire\Forms\Auth\RegisterForm;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\WithFileUploads;

#[Layout('layouts.guest')]
class Register extends Component
{
    use WithFileUploads;

    public RegisterForm $form;

    public function register(): void
    {
        $this->form->validate();

        /** @var string $disk */
        $disk = config('filesystems.default');

        $user = User::create([
            'username' => $this->form->username,
            'email' => $this->form->email,
            'password' => Hash::make($this->form->password),
            'profile_picture' => $this->form->profilePicture?->store('profile_pictures', $disk),
        ]);

        event(new Registered($user));

        Auth::login($user);

        $this->toastSuccess(__('Your account has been created ! You must now verify your email address.'));
        $this->redirectRoute('dashboard', navigate: true);
    }
}
