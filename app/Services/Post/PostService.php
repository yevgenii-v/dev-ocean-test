<?php

namespace App\Services\Post;

use App\Models\Post;
use App\Repositories\Post\Iterators\PostIterator;
use App\Repositories\Post\Iterators\PostWithCommentsIterator;
use App\Repositories\Post\PostRepository;
use App\Repositories\Post\PostStoreDTO;
use App\Repositories\Post\PostUpdateDTO;
use App\Services\User\UserAuthService;
use Exception;
use Illuminate\Support\Collection;

class PostService
{
    public function __construct(
        protected PostRepository $postRepository,
        protected UserAuthService $userAuthService,
    ) {
    }

    /**
     * @param int $lastId
     * @return Collection
     */
    public function pagination(int $lastId = 0): Collection
    {
        return $this->postRepository->get($lastId);
    }

    /**
     * @param PostStoreDTO $DTO
     * @return PostIterator
     */
    public function store(PostStoreDTO $DTO): PostIterator
    {
        $postIterator = $this->postRepository->store($DTO);

        return $this->postRepository->getById(
            $postIterator->getId(),
        );
    }

    /**
     * @param int $id
     * @return PostWithCommentsIterator|null
     * @throws Exception
     */
    public function getWithCommentsById(int $id): ?PostWithCommentsIterator
    {
        $post = $this->postRepository->getWithCommentsById($id);

        if ($post === null) {
            throw new Exception('This post doesn\'t belong to current user or not exist.', 404);
        }

        return $post;
    }

    /**
     * @param PostUpdateDTO $DTO
     * @return PostIterator
     * @throws Exception
     */
    public function update(PostUpdateDTO $DTO): PostIterator
    {
        if ($this->isPostBelongsToUser($DTO->getId()) === false) {
            throw new Exception('This post doesn\'t belong to current user or not exist.', 404);
        }

        $this->postRepository->update($DTO);

        return $this->postRepository->getById(
            $DTO->getId()
        );
    }

    /**
     * @throws Exception
     */
    public function forceDelete(int $id): void
    {
        if ($this->isPostBelongsToUser($id) === false) {
            throw new Exception('This post doesn\'t belong to current user or not exist.', 404);
        }

        $this->postRepository->forceDelete($id);
    }

    /**
     * @throws Exception
     */
    public function softDelete(int $id): void
    {
        if ($this->isPostBelongsToUser($id) === false) {
            throw new Exception('This post doesn\'t belong to current user or not exist.', 404);
        }

        $this->postRepository->softDelete($id);
    }

    /**
     * @param int $id
     * @return PostIterator|null
     * @throws Exception
     */
    public function restore(int $id): ?PostIterator
    {
        if ($this->isPostBelongsToUser($id) === false) {
            throw new Exception('This post doesn\'t belong to current user or not soft deleted.', 404);
        }

        $this->postRepository->restoreForUser($id);

        return $this->postRepository->getById($id);
    }

    /**
     * @throws Exception
     */
    public function publishForUser(int $id): ?PostIterator
    {
        if ($this->isPostBelongsToUser($id) === false) {
            throw new Exception('This post doesn\'t belong to current user or not exist.', 404);
        }

        if ($this->postRepository->isPublishedForUser($id) === false) {
            throw new Exception('This post is published already.', 400);
        }

        $this->postRepository->publishForUser($id);

        return $this->postRepository->getById($id);
    }

    /**
     * @throws Exception
     */
    public function unpublishForUser(int $id): void
    {
        if ($this->isPostBelongsToUser($id) === false) {
            throw new Exception('This post doesn\'t belong to current user or not exist.', 404);
        }

        if ($this->postRepository->isPublishedForUser($id) === true) {
            throw new Exception('This post is unpublished already.', 400);
        }

        $this->postRepository->unpublishForUser($id);
    }

    /**
     * Check if post belongs to user.
     *
     * @throws Exception
     */
    protected function isPostBelongsToUser(int $postId): bool
    {
        $post = $this->postRepository->getWithTrashedById($postId);

        if ($post === null) {
            throw new Exception('This post doesn\'t belong to current user or not exist.', 404);
        }

        return $post->getUser()->getId() === $this->userAuthService->getUserId();
    }
}
