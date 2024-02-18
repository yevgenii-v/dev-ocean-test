<?php

namespace App\Repositories\Post;

use App\Models\Post;
use App\Repositories\Comment\Iterators\CommentIterator;
use App\Repositories\Post\Iterators\PostIterator;
use App\Repositories\Post\Iterators\PostWithCommentsIterator;
use App\Services\User\UserAuthService;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

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
        $collection = Post::with(['user'])
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
    public function getById(int $id): ?PostIterator
    {
        $post = Post::with(['user'])
            ->whereId($id)
            ->first();

        if ($post === null) {
            return null;
        }

        return new PostIterator($post);
    }

    /**
     * @param int $id
     * @return PostWithCommentsIterator|null
     */
    public function getWithCommentsById(int $id): ?PostWithCommentsIterator
    {
        $post = Post::with(
            [
                'user',
                'comments' => function ($query) use ($id) {
                    $query->where('parent_id', '=', null);
                    $query->with(['recursiveComments' => function ($query) use ($id) {
                        $query->where('post_id', '=', $id);
                    }]);
                }
            ]
        )->whereId($id)
            ->where('published_at', '<>', null)
            ->first();

        if ($post === null) {
            return null;
        }

        return new PostWithCommentsIterator($post);
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
