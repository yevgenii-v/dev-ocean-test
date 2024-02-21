<?php

namespace App\Services\User\Login;

use App\Repositories\User\Iterators\UserIterator;
use Laravel\Passport\PersonalAccessTokenResult;

class LoginDTO
{
    protected PersonalAccessTokenResult $bearerToken;
    protected UserIterator $userIterator;

    public function __construct(
        protected string $email,
        protected string $password,
    ) {
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @return PersonalAccessTokenResult
     */
    public function getBearerToken(): PersonalAccessTokenResult
    {
        return $this->bearerToken;
    }

    /**
     * @param PersonalAccessTokenResult $bearerToken
     */
    public function setBearerToken(PersonalAccessTokenResult $bearerToken): void
    {
        $this->bearerToken = $bearerToken;
    }

    /**
     * @return UserIterator
     */
    public function getUserIterator(): UserIterator
    {
        return $this->userIterator;
    }

    /**
     * @param UserIterator $userIterator
     */
    public function setUserIterator(UserIterator $userIterator): void
    {
        $this->userIterator = $userIterator;
    }
}
