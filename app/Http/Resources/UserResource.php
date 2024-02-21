<?php

namespace App\Http\Resources;

use App\Repositories\User\Iterators\UserIterator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use OpenApi\Attributes as OA;

class UserResource extends JsonResource
{
    /**

     * @return array<string, mixed>
     */
    #[OA\Schema(
        schema: 'User',
        description: 'Show information about the user',
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
                property: 'email',
                type: 'string',
            ),
            new OA\Property(
                property: 'profilePhoto',
                type: 'string',
                nullable: true,
            ),
            new OA\Property(
                property: 'createdAt',
                type: 'string',
            ),
        ],
        example: [
            'id'            => 3,
            'login'         => 'John_Doe',
            'email'         => 'john.doe@gmail.com',
            'profilePhoto'  => null,
            'createdAt'     => '2024-02-19 20:35:36',
        ]
    )]
    public function toArray(Request $request): array
    {
        /** @var UserIterator $resource */
        $resource = $this->resource;
        return [
            'id'            => $resource->getId(),
            'login'         => $resource->getLogin(),
            'email'         => $resource->getEmail(),
            'profilePhoto'  => $resource->getProfilePhoto(),
            'createdAt'     => $resource->getCreatedAt(),
        ];
    }
}
