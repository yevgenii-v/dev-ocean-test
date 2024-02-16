<?php

namespace App\Services\User;

use App\Repositories\User\Iterators\UserIterator;
use App\Services\User\Login\LoginDTO;
use Laravel\Passport\PersonalAccessTokenResult;
use Laravel\Passport\Token;
use Laravel\Passport\TransientToken;

class UserAuthService
{
    /**
     * @param LoginDTO $DTO
     * @return bool
     */
    public function isCredentialsValid(LoginDTO $DTO): bool
    {
        return auth()->attempt([
            'email'     => $DTO->getEmail(),
            'password'  => $DTO->getPassword(),
        ]);
    }

    /**
     * @return int|string|null
     */
    public function getUserId(): int|string|null
    {
        return auth()->id();
    }

    public function getUserByBearerToken(): UserIterator
    {
        $user = auth('api')->user();

        return new UserIterator((object)[
            'id'            => $user->id,
            'login'         => $user->login,
            'email'         => $user->email,
            'profile_photo' => $user->profilePhoto,
            'created_at'    => $user->created_at,
        ]);
    }

    /**
     * @return PersonalAccessTokenResult
     */
    public function createUserToken(): PersonalAccessTokenResult
    {
        return auth()->user()->createToken(config('app.name'));
    }

    /**
     * @return Token|TransientToken|null
     */
    public function getUserToken(): Token|TransientToken|null
    {
        return auth('api')->user()->token();
    }
}
