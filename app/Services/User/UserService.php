<?php

namespace App\Services\User;

use App\Repositories\User\Iterators\UserIterator;
use App\Repositories\User\UserCreateDTO;
use App\Repositories\User\UserRepository;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserAuthService $userAuthService,
    ) {
    }

    /**
     * @param UserCreateDTO $DTO
     * @return UserIterator
     */
    public function register(UserCreateDTO $DTO): UserIterator
    {
        $userIterator = $this->userRepository->create($DTO);

        return $this->userRepository->findById(
            $userIterator->getId()
        );
    }
}
