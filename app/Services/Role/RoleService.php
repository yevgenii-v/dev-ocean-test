<?php

namespace App\Services\Role;

use App\Enums\RoleEnum;
use App\Repositories\User\UserRepository;
use App\Services\User\UserAuthService;
use Exception;

class RoleService
{
    /**
     * @param UserRepository $userRepository
     * @param UserAuthService $userAuthService
     */
    public function __construct(
        protected UserRepository $userRepository,
        protected UserAuthService $userAuthService,
    ) {
    }

    /**
     * Gives new role(s) for user.
     *
     * @param int $userId
     * @param RoleEnum ...$roles
     * @return void
     */
    public function submitRoles(int $userId, RoleEnum ...$roles): void
    {
        $user = $this->userRepository->getModelById($userId);

        $roleIds = collect($roles)->map(function ($role) {
            return $role->value;
        });

        $user->roles()->sync($roleIds);
    }
}
