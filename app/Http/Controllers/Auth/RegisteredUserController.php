<?php

declare(strict_types=1);

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        /** @var string $password */
        $password = $request->password;

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($password),
            'profile_picture' => $this->extractFile($request)?->store('profile_pictures'),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }

    private function extractFile(RegisterRequest $request): ?UploadedFile
    {
        $file = $request->file('profile_picture');

        if (is_array($file)) {
            $file = $file[0] ?? null;
        }

        return $file;
    }
}
