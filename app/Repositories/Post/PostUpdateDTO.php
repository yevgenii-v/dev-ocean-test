<?php

namespace App\Repositories\Post;

class PostUpdateDTO
{
    /**
     * @param int $id
     * @param string $title
     * @param string $description
     */
    public function __construct(
        protected int $id,
        protected string $title,
        protected string $description,
    ) {
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
}
