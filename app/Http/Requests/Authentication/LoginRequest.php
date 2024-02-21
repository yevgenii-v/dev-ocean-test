<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class LoginRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array|string>
     */
    #[OA\RequestBody(
        request: 'LoginRequest',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'email',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                ),
            ],
            type: 'object',
            example: [
                'email'         => '',
                'password'   => '',
            ],
        ),
    )]
    public function rules(): array
    {
        return [
            'email'     => ['required', 'max:255', 'email'],
            'password'  => ['required', 'string', 'max:255'],
        ];
    }
}
