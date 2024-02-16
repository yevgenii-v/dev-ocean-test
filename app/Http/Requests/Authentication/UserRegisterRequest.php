<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UserRegisterRequest extends FormRequest
{
    /**
     * Create a new user.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'login'     => ['string', 'required', 'unique:users,login', 'max:255'],
            'email'     => ['email:rfc,dns', 'required', 'unique:users,email','max:255'],
            'password'  => ['confirmed', 'max:255', 'required', Password::min(6)
                    ->mixedCase()
                    ->numbers(),
                ],
        ];
    }
}
