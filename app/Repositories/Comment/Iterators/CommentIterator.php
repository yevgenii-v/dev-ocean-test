<?php

namespace App\Repositories\Comment\Iterators;

use App\Repositories\User\Iterators\UserIterator;

class CommentIterator
{
    protected int $id;
    protected int|null $parentId;
    protected int $postId;
    protected UserIterator $user;
    protected string $body;
    protected string $createdAt;

    /**
     * @param object $data
     */
    public function __construct(object $data)
    {
        $this->id           = $data->id;
        $this->parentId     = $data->parent_id;
        $this->postId       = $data->post_id;
        $this->user         = new UserIterator($data->user);
        $this->body         = $data->body;
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
     * @return int|null
     */
    public function getParentId(): ?int
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
     * @return UserIterator
     */
    public function getUser(): UserIterator
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @return string
     */
    public function getCreatedAt(): string
    {
        return $this->createdAt;
    }
}
