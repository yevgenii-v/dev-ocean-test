<?php

namespace App\Repositories\User;

use App\Models\User;
use App\Repositories\User\Iterators\UserIterator;

class UserRepository
{
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

    public function findById(int $id): UserIterator
    {
        return new UserIterator(
            User::whereId($id)->first()
        );
    }
}
