<?php

namespace App\Http\Resources;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExceptionResource extends JsonResource
{
    /**
     * Show Exception message and code.
     *
     * @return array<string, mixed>
     */
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
