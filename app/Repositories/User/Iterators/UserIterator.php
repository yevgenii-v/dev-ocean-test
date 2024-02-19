<?php

namespace App\Repositories\User\Iterators;

class UserIterator
{
    protected int $id;
    protected string $login;
    protected string $email;
    protected ?string $profilePhoto;
    protected string|null $createdAt;

    public function __construct(object $data)
    {
        $this->id           = $data->id;
        $this->login        = $data->login;
        $this->email        = $data->email;
        $this->profilePhoto = $data->profile_photo;
        $this->createdAt    = $data->created_at;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
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
     * @return ?string
     */
    public function getProfilePhoto(): ?string
    {
        return $this->profilePhoto;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
