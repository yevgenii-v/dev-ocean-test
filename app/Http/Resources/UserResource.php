<?php

namespace App\Http\Resources;

use App\Repositories\User\Iterators\UserIterator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Get info about the user.
     *
     * @return array<string, mixed>
     */
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
