<?php

namespace App\Http\Resources\Errors;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class ValidationResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[OA\Schema(
        schema: 'Validation',
        description: 'Shows validation errors.',
        properties: [
            new OA\Property(
                property: 'message',
                type: 'string'
            ),
            new OA\Property(
                property: 'errors',
                description: "Each key describes error message.",
                properties: [
                    new OA\Property(
                        property: 'query or path name',
                        type: 'array',
                        items: new OA\Items(),
                    ),
                ],
                type: 'object'
            ),
        ],
        example: [
            'message' => 'The email field must be a valid email address. (and 3 more errors)',
            'errors' => [
                'email' => [
                    'The email field must be a valid email address.'
                ],
                'password' => [
                    "The password field confirmation does not match.",
                    "The password field must contain at least one uppercase and one lowercase letter.",
                    "The password field must contain at least one number."
                ],
            ],
        ]
    )]
    public function toArray(Request $request): array
    {
        /** @var ValidationException $validation */
        $validation = $this->resource;

        return [
            'message'      => $validation->getMessage(),
            'errors'       => $validation->errors(),
        ];
    }
}
