<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPostDestroyRequest;
use App\Http\Requests\Admin\AdminPostIndexRequest;
use App\Http\Requests\Admin\AdminPostShowRequest;
use App\Http\Resources\Admin\AdminPostResource;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\PostWithCommentsResource;
use App\Services\Post\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdminPostController extends Controller
{
    public function __construct(
        protected PostService $postService
    ) {
    }

    /**
     * @param AdminPostIndexRequest $request
     * @return JsonResponse
     */
    public function index(AdminPostIndexRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $service = $this->postService->paginationWithTrashed(...$validated);

        $lastId = is_null($service->last()) ? 0 : $service->last()->getId();

        $resource = AdminPostResource::collection($service);
        return $resource->additional([
            'lastId' => $lastId,
        ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @param AdminPostShowRequest $request
     * @return JsonResponse
     */
    public function show(AdminPostShowRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $service = $this->postService->getTrashedWithCommentsById($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        $resource = new PostWithCommentsResource($service);

        return $resource->response()->setStatusCode(200);
    }

    /**
     * @param AdminPostDestroyRequest $request
     * @return Response|JsonResponse
     */
    public function forceDelete(AdminPostDestroyRequest $request): Response|JsonResponse
    {
        try {
            $validated = $request->validated();
            $this->postService->forceDeleteWithTrashed($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        return response()->noContent();
    }
}
