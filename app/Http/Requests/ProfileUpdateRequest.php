<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Unique;

class ProfileUpdateRequest extends FormRequest
{
    /** @return array<string, array<int, Unique|string>> */
    public function rules(): array
    {
        return [
            'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($this->user()?->id)],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()?->id)],
        ];
    }
}
