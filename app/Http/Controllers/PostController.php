<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostDestroyRequest;
use App\Http\Requests\Post\PostIndexRequest;
use App\Http\Requests\Post\PostPublishRequest;
use App\Http\Requests\Post\PostRestoreRequest;
use App\Http\Requests\Post\PostShowRequest;
use App\Http\Requests\Post\PostSoftDeleteRequest;
use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Requests\Post\PostUnpublishRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostWithCommentsResource;
use App\Models\Post;
use App\Repositories\Post\PostStoreDTO;
use App\Repositories\Post\PostUpdateDTO;
use App\Services\Post\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class PostController extends Controller
{
    /**
     * @param PostService $postService
     */
    public function __construct(
        protected PostService $postService,
    ) {
    }

    /**
     * Display a listing of the posts.
     *
     * @param PostIndexRequest $request
     * @return JsonResponse
     */
    public function index(PostIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $service = $this->postService->pagination(...$validated);

        $lastId = is_null($service->last()) ? 0 : $service->last()->getId();

        return PostResource::collection($service)
            ->additional([
                'lastId' => $lastId,
            ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * Store a newly created post in storage.
     *
     * @param PostStoreRequest $request
     * @return JsonResponse
     */
    public function store(PostStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $DTO = new PostStoreDTO(...$validated);
        $service = $this->postService->store($DTO);
        $resource = new PostResource($service);

        return $resource->response()->setStatusCode(201);
    }

    /**
     * Display the specified post.
     *
     * @param PostShowRequest $request
     * @return JsonResponse
     * @throws Exception
     */
    public function show(PostShowRequest $request): JsonResponse
    {
        try {
            $validate = $request->validated();
            $service = $this->postService->getWithCommentsById($validate['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        $resource = new PostWithCommentsResource($service);

        return $resource->response()->setStatusCode(200);
    }

    /**
     * Update the specified post in storage.
     *
     * @param PostUpdateRequest $request
     * @return JsonResponse
     */
    public function update(PostUpdateRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $DTO = new PostUpdateDTO(...$validated);
            $service = $this->postService->update($DTO);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode(
                    $e->getCode()
                );
        }

        $resource = new PostResource($service);
        return $resource->response()->setStatusCode(200);
    }

    /**
     * Remove the specified post from storage.
     *
     * @param PostDestroyRequest $request
     * @return JsonResponse|Response
     */
    public function forceDelete(PostDestroyRequest $request): JsonResponse|Response
    {
        try {
            $validated = $request->validated();
            $this->postService->forceDelete($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        return response()->noContent()->setStatusCode(204);
    }

    /**
     * Hide the specified post from storage.
     *
     * @param PostSoftDeleteRequest $request
     * @return Response|JsonResponse
     */
    public function softDelete(PostSoftDeleteRequest $request): Response|JsonResponse
    {
        try {
            $validated = $request->validated();
            $this->postService->softDelete($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        return response()->noContent()->setStatusCode(204);
    }

    /**
     * Restore the post from soft delete.
     *
     * @param PostRestoreRequest $request
     * @return JsonResponse
     */
    public function restore(PostRestoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $service = $this->postService->restore($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        $resource = new PostResource($service);

        return $resource->response()->setStatusCode(200);
    }

    /**
     * Publish the post that belongs to the current user.
     *
     * @param PostPublishRequest $request
     * @return JsonResponse
     */
    public function publish(PostPublishRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $service = $this->postService->publishForUser($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }
        $resource = new PostResource($service);

        return $resource->response()->setStatusCode(200);
    }

    /**
     * Unpublish the post that belongs to the current user.
     *
     * @param PostUnpublishRequest $request
     * @return Response|JsonResponse
     */
    public function unpublish(PostUnpublishRequest $request): Response|JsonResponse
    {
        try {
            $validated = $request->validated();
            $this->postService->unpublishForUser($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        return response()->noContent()->setStatusCode(204);
    }
}
