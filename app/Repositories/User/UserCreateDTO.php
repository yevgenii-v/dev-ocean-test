<?php

namespace App\Repositories\User;

use Illuminate\Http\UploadedFile;

class UserCreateDTO
{
    public function __construct(
        protected string $login,
        protected string $email,
        protected string $password,
    ) {
    }

    /**
     * @return string
     */
    public function getLogin(): string
    {
        return $this->login;
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
     * @return UploadedFile
     */
    public function getProfilePhoto(): UploadedFile
    {
        return $this->profilePhoto;
    }
}
