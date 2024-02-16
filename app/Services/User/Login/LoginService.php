<?php

namespace App\Services\User\Login;

use App\Services\User\Login\Handlers\IsCredentialsValidHandler;
use App\Services\User\Login\Handlers\SetAccessTokenHandler;
use App\Services\User\Login\Handlers\SetAuthorizedUserHandler;
use Illuminate\Pipeline\Pipeline;

class LoginService
{
    const HANDLERS = [
        IsCredentialsValidHandler::class,
        SetAuthorizedUserHandler::class,
        SetAccessTokenHandler::class,
    ];

    public function __construct(
        protected Pipeline $pipeline,
    ) {
    }

    /**
     * @param LoginDTO $DTO
     * @return LoginDTO
     */
    public function handle(LoginDTO $DTO): LoginDTO
    {
        return $this->pipeline
            ->send($DTO)
            ->through(self::HANDLERS)
            ->then(function (LoginDTO $DTO) {
                return $DTO;
            });
    }
}
