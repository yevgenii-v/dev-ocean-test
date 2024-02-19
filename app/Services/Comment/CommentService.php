<?php

namespace App\Services\Comment;

use App\Repositories\Comment\CommentRepository;
use App\Repositories\Comment\CommentStoreDTO;
use App\Repositories\Comment\Iterators\CommentIterator;
use Exception;

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

    /**
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function forceDelete(int $id): void
    {
        if ($this->commentRepository->isExists($id) === false) {
            throw new Exception('This comment doesn\'t exist.', 400);
        }

        $this->commentRepository->forceDelete($id);
    }
}
