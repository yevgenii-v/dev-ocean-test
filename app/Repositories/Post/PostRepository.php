<?php

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\Post\Iterators\PostIterator;
use App\Services\User\UserAuthService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PostRepository
{
    /**
     * @param UserAuthService $userAuthService
     */
    public function __construct(
        protected UserAuthService $userAuthService,
    ) {
    }

    /**
     * @param int $lastId
     * @return Collection
     */
    public function get(int $lastId): Collection
    {
        $collection = Post::withoutTrashed()
            ->with(['user'])
            ->where('id', '>', $lastId)
            ->where('published_at', '<>', null)
            ->take(100)
            ->get();

        return $collection->map(function ($post) {
            return new PostIterator($post);
        });
    }

    /**
     * @param PostStoreDTO $DTO
     * @return PostIterator
     */
    public function store(PostStoreDTO $DTO): PostIterator
    {
        return new PostIterator(
            Post::create([
                'title' => $DTO->getTitle(),
                'description' => $DTO->getDescription(),
                'user_id' => $this->userAuthService->getUserIdByToken(),
            ])
        );
    }

    /**
     * @param int $id
     * @return PostIterator|null
     * @throws Exception
     */
    public function getWithTrashedById(int $id): ?PostIterator
    {
        $post = Post::withTrashed()
            ->with(['user'])
            ->where('id', $id)
            ->first();

        if ($post === null) {
            return null;
        }

        return new PostIterator($post);
    }

    /**
     * @param int $id
     * @return PostIterator|null
     */
    public function getWithoutTrashedById(int $id): ?PostIterator
    {
        $post = Post::withoutTrashed()
            ->with('user')
            ->whereId($id)
            ->where('published_at', '<>', null)
            ->first();

        if ($post === null) {
            return null;
        }

        return new PostIterator($post);
    }

    /**
     * @param PostUpdateDTO $DTO
     * @return void
     */
    public function update(PostUpdateDTO $DTO): void
    {
        Post::find($DTO->getId())
            ->fill([
                'title'         => $DTO->getTitle(),
                'description'   => $DTO->getDescription(),
            ])->save();
    }

    /**
     * @param int $id
     * @return void
     */
    public function forceDelete(int $id): void
    {
        Post::whereId($id)->forceDelete();
    }

    /**
     * @param int $id
     * @return void
     */
    public function softDelete(int $id): void
    {
        Post::whereId($id)->delete();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isTrashed(int $id): bool
    {
        return Post::onlyTrashed()
            ->where('id', '=', $id)
            ->exists();
    }

    /**
     * @param int $id
     * @return void
     */
    public function restoreForUser(int $id): void
    {
        Post::onlyTrashed()
            ->find($id)
            ->where('user_id', '=', $this->userAuthService->getUserIdByToken())
            ->restore();
    }

    /**
     * @param int $id
     * @return bool
     */
    public function isPublishedForUser(int $id): bool
    {
        return Post::withoutTrashed()
            ->whereId($id)
            ->where('user_id', '=', $this->userAuthService->getUserIdByToken())
            ->where('published_at', '=', null)
            ->exists();
    }

    public function publishForUser(int $id): void
    {
        Post::withoutTrashed()
            ->whereId($id)
            ->where('user_id', '=', $this->userAuthService->getUserIdByToken())
            ->update([
                'published_at' => Carbon::now(),
            ]);
    }

    /**
     * @param int $id
     * @return void
     */
    public function unpublishForUser(int $id): void
    {
        Post::withoutTrashed()
            ->whereId($id)
            ->where('user_id', '=', $this->userAuthService->getUserIdByToken())
            ->update([
                'published_at' => null,
            ]);
    }
}
