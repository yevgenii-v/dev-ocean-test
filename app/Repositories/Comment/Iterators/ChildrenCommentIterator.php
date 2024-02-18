<?php

namespace App\Repositories\Comment\Iterators;

use App\Repositories\User\Iterators\UserIterator;
use Illuminate\Support\Collection;

class ChildrenCommentIterator
{
    protected int $id;
    protected Collection $reply;
    protected int $postId;
    protected UserIterator $user;
    protected string $body;
    protected string $createdAt;

    public function __construct(object $data)
    {
        $this->id       = $data->id;
        $this->reply    = $data->childrenComment;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getReply(): Collection
    {
        return $this->reply;
    }

//    /**
//     * @return int
//     */
//    public function getPostId(): int
//    {
//        return $this->postId;
//    }
//
//    /**
//     * @return UserIterator
//     */
//    public function getUser(): UserIterator
//    {
//        return $this->user;
//    }
//
//    /**
//     * @return string
//     */
//    public function getBody(): string
//    {
//        return $this->body;
//    }
//
//    /**
//     * @return string
//     */
//    public function getCreatedAt(): string
//    {
//        return $this->createdAt;
//    }
}
