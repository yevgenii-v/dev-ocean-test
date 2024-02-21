<?php

namespace App\Repositories\Comment;

use App\Models\Comment;
use App\Repositories\Comment\Iterators\CommentIterator;
use App\Services\User\UserAuthService;
use Carbon\Carbon;

class CommentRepository
{
    public function __construct(
        protected UserAuthService $userAuthService,
    ) {
    }

    public function store(CommentStoreDTO $DTO): CommentIterator
    {
        return new CommentIterator(
            Comment::create(
                [
                    'parent_id'     => $DTO->getParentId(),
                    'post_id'       => $DTO->getPostId(),
                    'user_id'       => $this->userAuthService->getUserIdByToken(),
                    'body'          => $DTO->getBody(),
                ]
            )
        );
    }

    /**
     * @param int $id
     * @return CommentIterator
     */
    public function getById(int $id): CommentIterator
    {
        return new CommentIterator(
            Comment::with(['user'])
                ->where('id', '=', $id)
                ->first()
        );
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isExists(int $id): bool
    {
        return Comment::whereId($id)->exists();
    }

    /**
     * @param int $id
     * @return void
     */
    public function forceDelete(int $id): void
    {
        Comment::whereId($id)->forceDelete();
    }
}
