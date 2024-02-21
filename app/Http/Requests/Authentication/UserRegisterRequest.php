<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;
use OpenApi\Attributes as OA;

class UserRegisterRequest extends FormRequest
{
    /**
     * Create a new user.
     *
     * @return array<string, ValidationRule|array|string>
     */
    #[OA\RequestBody(
        request: 'UserRegisterRequest',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'login',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'email',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'password_confirmation',
                    type: 'string',
                ),
            ],
            type: 'object',
            example: [
                'login'                     => '',
                'email'                     => '',
                'password'                  => '',
                'password_confirmation'     => '',
            ],
        ),
    )]
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
