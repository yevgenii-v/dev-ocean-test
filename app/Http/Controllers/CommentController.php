<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comment\CommentStoreRequest;
use App\Http\Resources\CommentResource;
use App\Repositories\Comment\CommentStoreDTO;
use App\Services\Comment\CommentService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

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
    #[OA\Post(
        path: '/v1/comments',
        summary: 'Create a new comment.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            ref: '#/components/requestBodies/CommentStoreRequest',
        ),
        tags: ['Comments'],
        responses: [
            new OA\Response(
                response: 204,
                description: '',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Comment',
                        ),
                    ],
                ),
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
                description: 'Forbidden',
                content: new OA\JsonContent(
                    examples: [
                        new OA\Examples(
                            example: 'The user is banned',
                            summary: '',
                            value: ['message' => 'You has been banned.'],
                        ),
                        new OA\Examples(
                            example: 'The user has\'nt right permissions.',
                            summary: '',
                            value: ['message' => 'Permission denied.'],
                        ),
                    ],
                    ref: '#/components/schemas/MiddlewareError',
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
    public function store(CommentStoreRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $DTO = new CommentStoreDTO(...$validated);

        $service = $this->commentService->store($DTO);
        $resource = new CommentResource($service);

        return $resource->response()->setStatusCode(201);
    }
}
