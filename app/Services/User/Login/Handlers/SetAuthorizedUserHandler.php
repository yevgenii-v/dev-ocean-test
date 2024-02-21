<?php

namespace App\Services\User\Login\Handlers;

use App\Repositories\User\UserRepository;
use App\Services\User\Login\LoginDTO;
use App\Services\User\Login\LoginInterface;
use App\Services\User\UserAuthService;
use Closure;

class SetAuthorizedUserHandler implements LoginInterface
{
    public function __construct(
        protected UserRepository $userRepository,
        protected UserAuthService $userAuthService,
    ) {
    }

    /**
     * @param LoginDTO $DTO
     * @param Closure $next
     * @return LoginDTO
     */
    public function handle(LoginDTO $DTO, Closure $next): LoginDTO
    {
        $userIterator = $this->userRepository->getById(
            $this->userAuthService->getUserId()
        );

        $DTO->setUserIterator($userIterator);

        return $next($DTO);
    }
}
