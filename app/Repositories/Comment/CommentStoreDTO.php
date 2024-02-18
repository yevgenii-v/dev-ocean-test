<?php

namespace App\Repositories\Comment;

class CommentStoreDTO
{
    public function __construct(
        protected int|null $parentId,
        protected int $postId,
        protected string $body,
    ) {
    }

    /**
     * @return int|null
     */
    public function getParentId(): int|null
    {
        return $this->parentId;
    }

    /**
     * @return int
     */
    public function getPostId(): int
    {
        return $this->postId;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }
}
