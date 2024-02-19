<?php

namespace App\Services\User;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Repositories\User\Iterators\AdminUserIterator;
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

    /**
     * @return int|string|null
     */
    public function getUserIdByToken(): int|string|null
    {
        return auth('api')->id();
    }

    /**
     * @return UserIterator
     */
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

    /**
     * Check authenticated user's role.
     *
     * @param RoleEnum $role
     * @return bool
     */
    public function isUserHasRole(RoleEnum $role): bool
    {
        /** @var User $user */
        $user = auth('api')->user();

        return $user
            ->roles()
            ->where('role_id', $role->value)
            ->exists();
    }

    public function isUserBanned(): bool
    {
        /** @var User $user */
        $user = auth('api')->user();

        $userIterator = new AdminUserIterator((object)[
            'id'            => $user->id,
            'login'         => $user->login,
            'email'         => $user->email,
            'profile_photo' => $user->profilePhoto,
            'created_at'    => $user->created_at,
            'updated_at'    => $user->updated_at,
            'is_banned'     => $user->is_banned,
        ]);

        return $userIterator->getIsBanned() === true;
    }
}
