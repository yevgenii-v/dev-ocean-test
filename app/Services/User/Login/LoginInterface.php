<?php

namespace App\Services\User\Login;

use Closure;

interface LoginInterface
{
    /**
     * @param LoginDTO $DTO
     * @param Closure $next
     * @return LoginDTO
     */
    public function handle(LoginDTO $DTO, Closure $next): LoginDTO;
}
