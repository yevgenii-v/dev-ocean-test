<?php

namespace App\Repositories\Post;

use Carbon\Carbon;

class PostStoreDTO
{
    /**
     * @param string $title
     * @param string $description
     */
    public function __construct(
        protected string $title,
        protected string $description,
    ) {
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
