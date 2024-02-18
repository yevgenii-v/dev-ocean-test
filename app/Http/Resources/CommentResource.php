<?php

namespace App\Http\Resources;

use App\Repositories\Comment\Iterators\CommentIterator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var CommentIterator $resource */
        $resource = $this->resource;

        return [
            'id'        => $resource->getId(),
            'parentId'  => $resource->getParentId(),
            'postId'    => $resource->getPostId(),
            'user'      => new UserResource($resource->getUser()),
            'body'      => $resource->getBody(),
            'createdAt' => $resource->getCreatedAt(),
        ];
    }
}
