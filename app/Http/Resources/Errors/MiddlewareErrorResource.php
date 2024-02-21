<?php

namespace App\Http\Resources\Errors;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Validation\ValidationException;
use OpenApi\Attributes as OA;

class MiddlewareErrorResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[OA\Schema(
        schema: 'MiddlewareError',
        description: 'Middleware error.',
        properties: [
            new OA\Property(
                property: 'message',
                type: 'string'
            ),
        ],
    )]
    public function toArray(Request $request): array
    {
        return [];
    }
}
