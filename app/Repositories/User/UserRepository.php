<?php

namespace App\Repositories\User;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Repositories\User\Iterators\AdminUserIterator;
use App\Repositories\User\Iterators\UserIterator;

class UserRepository
{
    /**
     * @param UserCreateDTO $DTO
     * @return UserIterator
     */
    public function create(UserCreateDTO $DTO): UserIterator
    {
        return new UserIterator(
            User::create([
                'login'         => $DTO->getLogin(),
                'email'         => $DTO->getEmail(),
                'password'      => $DTO->getPassword(),
            ])
        );
    }

    /**
     * @param int $id
     * @return UserIterator
     */
    public function getById(int $id): UserIterator
    {
        return new UserIterator(
            User::whereId($id)->first()
        );
    }

    /**
     * @param int $id
     * @return User
     */
    public function getModelById(int $id): User
    {
        return User::whereId($id)->first();
    }

    /**
     * @param int $id
     * @return AdminUserIterator
     */
    public function getByIdForAdmin(int $id): AdminUserIterator
    {
        return new AdminUserIterator(
            User::whereId($id)->first()
        );
    }

    /**
     * @param int $id
     * @return void
     */
    public function ban(int $id): void
    {
        User::whereId($id)
            ->update([
                'is_banned' => true,
            ]);
    }

    /**
     * @param int $id
     * @return void
     */
    public function restore(int $id): void
    {
        User::whereId($id)
            ->update([
                'is_banned' => false,
            ]);
    }
}
