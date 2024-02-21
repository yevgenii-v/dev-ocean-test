<?php

namespace App\Http\Resources\Errors;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class ExceptionResource extends JsonResource
{
    /**
     * Show Exception message and code.
     *
     * @return array<string, mixed>
     */
    #[OA\Schema(
        schema: 'Error',
        description: 'Show exception error.',
        properties: [
            new OA\Property(
                property: 'message',
                type: 'string',
            ),
            new OA\Property(
                property: 'code',
                type: 'integer',
            ),
        ],
    )]
    public function toArray(Request $request): array
    {
        /** @var Exception $resource */
        $resource = $this->resource;

        return [
            'message' => $resource->getMessage(),
            'code'    => $resource->getCode(),
        ];
    }
}
