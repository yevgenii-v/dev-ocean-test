<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChildrenCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var Comment $resource */
        $resource = $this->resource;

        return [
            'id'        => $resource->id,
            'login'     => $resource->user->login,
            'body'      => $resource->body,
            'createdAt' => $resource->created_at,
            'comments'  => ChildrenCommentResource::collection($resource->recursiveComments),
        ];
    }
}
