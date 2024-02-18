<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use App\Repositories\Comment\CommentStoreDTO;
use App\Services\Comment\CommentService;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    /**
     * @param CommentService $commentService
     */
    public function __construct(
        protected CommentService $commentService,
    ) {
    }

    /**
     * @param CommentStoreRequest $request
     * @return JsonResponse
     */
    public function store(CommentStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $DTO = new CommentStoreDTO(...$validated);

        $service = $this->commentService->store($DTO);
        $resource = new CommentResource($service);

        return $resource->response()->setStatusCode(201);
    }
}
