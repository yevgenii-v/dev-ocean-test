<?php

namespace App\Services\User\Login\Handlers;

use App\Services\User\Login\LoginDTO;
use App\Services\User\Login\LoginInterface;
use App\Services\User\UserAuthService;
use Closure;

class SetAccessTokenHandler implements LoginInterface
{
    public function __construct(
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
        $DTO->setBearerToken(
            $this->userAuthService->createUserToken()
        );

        return $next($DTO);
    }
}
