<?php

namespace App\Http\Resources;

use App\Repositories\Post\Iterators\PostWithCommentsIterator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class PostWithCommentsResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    #[OA\Schema(
        schema: 'PostWithComments',
        description: 'Show information about the post with comments.',
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
            new OA\Property(
                property: 'comments',
                ref: '#/components/schemas/ChildrenComment'
            ),
        ],
        example: [
            [
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
                'comments'      => [
                    [
                        'id' => 1,
                        'login'         => 'John_Doe',
                        'body'          => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                        'createdAt'     => '2024-02-19 10:02:33',
                        'comments'      => [
                            [
                                'id' => 3,
                                'login'         => 'Jake_Doe',
                                'body'          => 'Mauris sem dui, semper at lacinia ac, elementum id leo.',
                                'createdAt'     => '2024-02-19 10:10:13',
                                'comments'      => [
                                    [
                                        'id' => 5,
                                        'login'         => 'John_Doe',
                                        'body'          => 'Curabitur ut accumsan purus, quis porta erat.',
                                        'createdAt'     => '2024-02-19 10:20:00',
                                        'comments'      => [],
                                    ],
                                ],
                            ],
                            [
                                'id' => 4,
                                'login'         => 'Jane_Doe',
                                'body'          => 'Praesent fringilla aliquet sem id ultrices.',
                                'createdAt'     => '2024-02-19 10:15:20',
                                'comments'      => [],
                            ]
                        ],
                    ],
                    [
                        'id' => 2,
                        'login'         => 'Jane_Doe',
                        'body'          => 'Etiam bibendum urna nec velit venenatis ultrices.',
                        'createdAt'     => '2024-02-19 10:05:10',
                        'comments'      => [
                            [
                                'id' => 6,
                                'login'         => 'John_Doe',
                                'body'          => 'Vestibulum aliquet venenatis nunc, eu laoreet lectus lobortis in.',
                                'createdAt'     => '2024-02-19 10:25:00',
                                'comments'      => [],
                            ],
                        ],
                    ],
                    [
                        'id' => 7,
                        'login'         => 'John_Doe',
                        'body'          => 'Vivamus a enim laoreet, mattis ipsum sit amet, tincidunt dui.',
                        'createdAt'     => '2024-02-19 10:35:01',
                        'comments'      => [],
                    ],
                ]
            ],

        ]
    )]
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
