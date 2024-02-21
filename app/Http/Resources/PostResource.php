<?php

namespace App\Http\Resources;

use App\Repositories\Post\Iterators\PostIterator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class PostResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[OA\Schema(
        schema: 'Post',
        description: 'Show information about the post.',
        properties: [
            new OA\Property(
                property: 'id',
                type: 'integer',
            ),
            new OA\Property(
                property: 'title',
                type: 'string',
            ),
            new OA\Property(
                property: 'description',
                type: 'string',
            ),
            new OA\Property(
                property: 'user',
                ref: '#/components/schemas/User'
            ),
            new OA\Property(
                property: 'publishedAt',
                type: 'string',
            ),
            new OA\Property(
                property: 'createdAt',
                type: 'string',
            ),
        ],
        example: [
            'id' => 1,
            'title'         => 'illo aut quae',
            'description'   => 'Cupiditate eos reprehenderit alias',
            'user' => [
                'id'            => 3,
                'login'         => 'John_Doe',
                'email'         => 'john.doe@gmail.com',
                'profilePhoto'  => null,
                'createdAt'     => '2024-02-19 20:35:36'
            ],
            'publishedAt'   => '2024-02-19 23:02:56',
            'createdAt'     => '2024-02-19 23:02:33',
        ]
    )]
    public function toArray(Request $request): array
    {
        /** @var PostIterator $resource */
        $resource = $this->resource;

        return [
            'id'            => $resource->getId(),
            'title'         => $resource->getTitle(),
            'description'   => $resource->getDescription(),
            'user'          => new UserResource($resource->getUser()),
            'publishedAt'   => $resource->getPublishedAt(),
            'createdAt'     => $resource->getCreatedAt(),
        ];
    }
}
