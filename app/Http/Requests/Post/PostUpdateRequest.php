<?php

namespace App\Http\Requests\Post;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use OpenApi\Attributes as OA;

class PostUpdateRequest extends FormRequest
{
    /**
     * @return array<string, ValidationRule|array|string>
     */
    #[OA\RequestBody(
        request: 'PostUpdateRequest',
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
            'id'            => ['integer', 'required'],
            'title'         => ['string', 'max:255'],
            'description'   => ['string', 'max:10000'],
        ];
    }

    /**
     * @return void
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'id' => $this->route('post'),
        ]);
    }
}
