<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\UserResource;
use App\Repositories\Post\Iterators\AdminPostIterator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var AdminPostIterator $resource */
        $resource = $this->resource;


        if ($resource->getId() === null) {
            return [];
        }

        return [
            'id'            => $resource->getId(),
            'title'         => $resource->getTitle(),
            'description'   => $resource->getDescription(),
            'user'          => new UserResource($resource->getUser()),
            'publishedAt'   => $resource->getPublishedAt(),
            'createdAt'     => $resource->getCreatedAt(),
            'deletedAt'     => $resource->getDeletedAt(),
        ];
    }
}
