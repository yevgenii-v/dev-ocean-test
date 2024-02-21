<?php

namespace App\Http\Resources;

use App\Repositories\Comment\Iterators\CommentIterator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class CommentResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[OA\Schema(
        schema: 'Comment',
        description: 'Show information about created comment.',
        properties: [
            new OA\Property(
                property: 'id',
                type: 'integer',
            ),
            new OA\Property(
                property: 'parentId',
                type: 'integer',
            ),
            new OA\Property(
                property: 'postId',
                type: 'integer',
            ),
            new OA\Property(
                property: 'user',
                ref: '#/components/schemas/User',
            ),
            new OA\Property(
                property: 'body',
                type: 'string',
            ),
            new OA\Property(
                property: 'createdAt',
                type: 'string',
            ),
        ],
        example: [
            'id'            => 1,
            'parentId'      => null,
            'postId'        => 2,
            'user'          => [
                'id'            => 3,
                'login'         => 'John_Doe',
                'email'         => 'john.doe@gmail.com',
                'profilePhoto'  => null,
                'createdAt'     => '2024-02-19 20:35:36',
            ],
            'body'          => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
            'createdAt'     => '2024-02-18 19:46:10',
        ]
    )]
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
