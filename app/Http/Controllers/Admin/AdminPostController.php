<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminPostDestroyRequest;
use App\Http\Requests\Admin\AdminPostIndexRequest;
use App\Http\Requests\Admin\AdminPostShowRequest;
use App\Http\Resources\Admin\AdminPostResource;
use App\Http\Resources\Errors\ExceptionResource;
use App\Http\Resources\PostWithCommentsResource;
use App\Services\Post\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

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
    #[OA\Get(
        path: '/v1/admin/posts',
        summary: 'Get one hundred posts with trashed by the last ID',
        security: [['bearerAuth' => []]],
        tags: ['Admin Panel'],
        parameters: [
            new OA\Parameter(
                name: 'lastId',
                in: 'query',
                schema: new OA\Schema(
                    type: 'string',
                ),
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Post',
                        ),
                        new OA\Property(
                            property: 'lastId',
                            type: 'integer',
                            example: 100
                        ),
                    ]
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
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Validation',
                        ),
                    ],
                ),
            )
        ],
    )]
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
    #[OA\Get(
        path: '/v1/admin/posts/{id}',
        summary: 'Show information about the post include trashed with comments.',
        security: [['bearerAuth' => []]],
        tags: ['Admin Panel'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Post ID',
                in: 'path',
                schema: new OA\Schema(
                    type: 'int',
                )
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/PostWithComments',
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
                description: 'User banned message.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message' => 'You has been banned.'],
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Error: Not Found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Error',
                        ),
                    ],
                    example: [
                        'data' => [
                            'message'   => 'This post doesn\'t not exist.',
                            'code'      => 404,
                        ],
                    ]
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

    #[OA\Delete(
        path: '/v1/admin/posts/{id}',
        summary: 'Permanent delete post.',
        security: [['bearerAuth' => []]],
        tags: ['Admin Panel'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'Post ID',
                in: 'path',
                schema: new OA\Schema(
                    type: 'integer',
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
                response: 404,
                description: 'Error: Not Found',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Error',
                    example: [
                        'data' => [
                            'message' => 'This post doesn\'t  exist.',
                            'code' => 404,
                        ],
                    ],
                ),
            ),
        ],
    )]
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
