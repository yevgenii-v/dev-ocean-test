<?php

namespace App\Repositories\Post\Iterators;

use App\Repositories\User\Iterators\UserIterator;
use Carbon\Carbon;

class PostIterator
{
    protected int $id;
    protected string $title;
    protected string $description;
    protected UserIterator $user;
    protected string|null $publishedAt;
    protected string|null $createdAt;

    public function __construct(object $data)
    {
        $this->id           = $data->id;
        $this->title        = $data->title;
        $this->description  = $data->description;
        $this->user         = new UserIterator($data->user);
        $this->publishedAt  = $data->published_at;
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return UserIterator
     */
    public function getUser(): UserIterator
    {
        return $this->user;
    }

    /**
     * @return string|null
     */
    public function getPublishedAt(): string|null
    {
        return $this->publishedAt;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): string|null
    {
        return $this->createdAt;
    }
}
