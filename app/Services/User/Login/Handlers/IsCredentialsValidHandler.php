<?php

namespace App\Services\User\Login\Handlers;

use App\Services\User\Login\LoginDTO;
use App\Services\User\Login\LoginInterface;
use App\Services\User\UserAuthService;
use Closure;
use Exception;

class IsCredentialsValidHandler implements LoginInterface
{
    public function __construct(
        protected UserAuthService $userAuthService,
    ) {
    }

    /**
     * @param LoginDTO $DTO
     * @param Closure $next
     * @return LoginDTO
     * @throws Exception
     */
    public function handle(LoginDTO $DTO, Closure $next): LoginDTO
    {
        if ($this->userAuthService->isCredentialsValid($DTO) === false) {
            throw new Exception('Credentials do not match.', 400);
        }

        return $next($DTO);
    }
}
