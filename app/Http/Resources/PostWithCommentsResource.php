<?php

namespace App\Http\Resources;

use App\Models\Post;
use App\Repositories\Post\Iterators\PostWithCommentsIterator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostWithCommentsResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var PostWithCommentsIterator $resource */
        $resource = $this->resource;

        return [
            'id'            => $resource->getId(),
            'title'         => $resource->getTitle(),
            'description'   => $resource->getDescription(),
            'user'          => new UserResource($resource->getUser()),
            'publishedAt'   => $resource->getPublishedAt(),
            'createdAt'     => $resource->getCreatedAt(),
            'comments'      => ChildrenCommentResource::collection($resource->getComments()),
        ];
    }
}
