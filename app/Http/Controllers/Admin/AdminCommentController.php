<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCommentDestroyRequest;
use App\Http\Resources\ExceptionResource;
use App\Services\Comment\CommentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AdminCommentController extends Controller
{
    /**
     * @param CommentService $commentService
     */
    public function __construct(
        protected CommentService $commentService,
    ) {
    }

    /**
     * @param AdminCommentDestroyRequest $request
     * @return Response|JsonResponse
     */
    public function destroy(AdminCommentDestroyRequest $request): Response|JsonResponse
    {
        try {
            $validated = $request->validated();
            $this->commentService->forceDelete($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        return response()->noContent();
    }
}
