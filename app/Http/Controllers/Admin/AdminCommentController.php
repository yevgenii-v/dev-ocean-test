<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminCommentForceDeleteRequest;
use App\Http\Resources\Errors\ExceptionResource;
use App\Services\Comment\CommentService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

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
     * @param AdminCommentForceDeleteRequest $request
     * @return Response|JsonResponse
     */
    #[OA\Delete(
        path: '/v1/admin/comments/{id}',
        summary: 'Permanently delete a comment.',
        security: [['bearerAuth' => []]],
        tags: ['Admin Panel'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Comment ID',
                in: 'path',
                schema: new OA\Schema(
                    type: 'int',
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 204,
                description: '',
                headers: [
                    new OA\Header(
                        header: 'cache-control',
                        description: 'no-cache,private',
                        schema: new OA\Schema(
                            type: 'string',
                        ),
                    ),
                ],
                content: [
                    new OA\JsonContent(
                        example: null,
                    ),
                ],
            ),
            new OA\Response(
                response: 401,
                description: 'Error: Unauthorized',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message' => 'Unauthorized.'],
                ),
            ),
            new OA\Response(
                response: 403,
                description: 'User banned message.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message' => 'You has been banned.'],
                ),
            ),
            new OA\Response(
                response: 422,
                description: 'Validation errors response.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Validation'
                ),
            ),
        ],
    )]
    public function forceDelete(AdminCommentForceDeleteRequest $request): Response|JsonResponse
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
