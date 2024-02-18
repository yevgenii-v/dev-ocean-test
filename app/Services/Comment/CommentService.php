<?php

namespace App\Services\Comment;

use App\Repositories\Comment\CommentRepository;
use App\Repositories\Comment\CommentStoreDTO;
use App\Repositories\Comment\Iterators\CommentIterator;

class CommentService
{
    /**
     * @param CommentRepository $commentRepository
     */
    public function __construct(
        protected CommentRepository $commentRepository,
    ) {
    }

    /**
     * @param CommentStoreDTO $DTO
     * @return CommentIterator
     */
    public function store(CommentStoreDTO $DTO): CommentIterator
    {
        $comment = $this->commentRepository->store($DTO);

        return  $this->commentRepository->getById(
            $comment->getId(),
        );
    }
}
