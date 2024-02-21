<?php

namespace App\Http\Requests\Post;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class PostStoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    #[OA\RequestBody(
        request: 'PostStoreRequest',
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'title',
                    type: 'string',
                ),
                new OA\Property(
                    property: 'description',
                    type: 'string',
                ),
            ],
            type: 'object',
            example: [
                'title'         => 'Lorem ipsum dolor',
                'description'   => 'Etiam bibendum urna nec velit venenatis ultrices.',
            ],
        ),
    )]
    public function rules(): array
    {
        return [
            'title'         => ['string', 'max:255', 'required'],
            'description'   => ['string', 'max:10000', 'required'],
        ];
    }
}
