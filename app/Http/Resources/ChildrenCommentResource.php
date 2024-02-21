<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class ChildrenCommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[OA\Schema(
        schema: 'ChildrenComment',
        description: 'Show information about the parent and children comments.',
        properties: [
            new OA\Property(
                property: 'id',
                type: 'integer',
            ),
            new OA\Property(
                property: 'login',
                type: 'string',
            ),
            new OA\Property(
                property: 'body',
                type: 'string',
            ),
            new OA\Property(
                property: 'createdAt',
                type: 'string',
            ),
            new OA\Property(
                property: 'comments',
                ref: '#/components/schemas/ChildrenComment',
            ),
        ],
    )]
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
