<?php

namespace App\Services\User;

use App\Enums\RoleEnum;
use App\Repositories\User\Iterators\AdminUserIterator;
use App\Repositories\User\Iterators\UserIterator;
use App\Repositories\User\UserCreateDTO;
use App\Repositories\User\UserRepository;
use App\Services\Role\RoleService;
use Exception;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserAuthService $userAuthService,
        protected RoleService $roleService,
    ) {
    }

    protected const GENERAL_ADMIN_ID = 1;

    /**
     * @param UserCreateDTO $DTO
     * @return UserIterator
     */
    public function register(UserCreateDTO $DTO): UserIterator
    {
        $userIterator = $this->userRepository->create($DTO);

        $this->roleService->submitRoles($userIterator->getId(), RoleEnum::User);

        return $this->userRepository->getById(
            $userIterator->getId()
        );
    }

    /**
     * Ban a user.
     * @throws Exception
     */
    public function ban(int $userId): void
    {
        $user = $this->userRepository->getByIdForAdmin($userId);
        $authUserId = $this->userAuthService->getUserIdByToken();

        if ($user->getId() === $authUserId) {
            throw new Exception('You can\'t ban yourself!', 400);
        }

        if ($user->getIsBanned() === true) {
            throw new Exception('This user is already banned.', 200);
        }

        if ($user->getId() === self::GENERAL_ADMIN_ID) {
            throw new Exception('Permission Denied.', 403);
        }

        $this->userRepository->ban($userId);
    }

    /**
     * Unban a user.
     *
     * @param int $userId
     * @return AdminUserIterator
     * @throws Exception
     */
    public function restore(int $userId): AdminUserIterator
    {
        $user = $this->userRepository->getByIdForAdmin($userId);

        if ($user->getIsBanned() === false) {
            throw new Exception('This user isn\'t banned.', 400);
        }

        $this->userRepository->restore($userId);

        return $this->userRepository->getByIdForAdmin($userId);
    }
}
