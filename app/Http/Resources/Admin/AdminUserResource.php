<?php

namespace App\Http\Resources\Admin;

use App\Repositories\User\Iterators\AdminUserIterator;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminUserResource extends JsonResource
{
    /**
     * Get info about the user.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var AdminUserIterator $resource */
        $resource = $this->resource;
        return [
            'id'            => $resource->getId(),
            'login'         => $resource->getLogin(),
            'email'         => $resource->getEmail(),
            'profilePhoto'  => $resource->getProfilePhoto(),
            'createdAt'     => $resource->getCreatedAt(),
            'updatedAt'     => $resource->getUpdatedAt(),
            'inBanned'      => $resource->getIsBanned(),
        ];
    }
}
