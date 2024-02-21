<?php

namespace App\Http\Controllers;

use App\Http\Requests\Post\PostForceDeleteRequest;
use App\Http\Requests\Post\PostIndexRequest;
use App\Http\Requests\Post\PostPublishRequest;
use App\Http\Requests\Post\PostRestoreRequest;
use App\Http\Requests\Post\PostShowRequest;
use App\Http\Requests\Post\PostSoftDeleteRequest;
use App\Http\Requests\Post\PostStoreRequest;
use App\Http\Requests\Post\PostUnpublishRequest;
use App\Http\Requests\Post\PostUpdateRequest;
use App\Http\Resources\Errors\ExceptionResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostWithCommentsResource;
use App\Repositories\Post\PostStoreDTO;
use App\Repositories\Post\PostUpdateDTO;
use App\Services\Post\PostService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

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
    #[OA\Get(
        path: '/v1/posts',
        summary: 'Get one hundred posts by the last ID',
        tags: ['Posts'],
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
    #[OA\Post(
        path: '/v1/posts',
        summary: 'Create a new post.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            ref: '#/components/requestBodies/PostStoreRequest',
        ),
        tags: ['Posts'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'Created.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Post',
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
                response: 422,
                description: 'Validation errors response.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Validation'
                ),
            ),
        ],
    )]
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
    #[OA\Get(
        path: '/v1/posts/{id}',
        summary: 'Show information about the post with comments.',
        tags: ['Posts'],
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
                            'message'   => 'The post is not exists.',
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
    #[OA\Patch(
        path: '/v1/posts/{id}',
        summary: 'Update a post.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            ref: '#/components/requestBodies/PostUpdateRequest',
        ),
        tags: ['Posts'],
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
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Post',
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
                    ref: '#/components/schemas/Error',
                    example: [
                        'data' => [
                            'message' => 'This post doesn\'t belong to current user or not exist.',
                            'code' => 404,
                        ],
                    ],
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
     * @param PostForceDeleteRequest $request
     * @return JsonResponse|Response
     */
    #[OA\Delete(
        path: '/v1/posts/{id}',
        summary: 'Permanent delete post.',
        security: [['bearerAuth' => []]],
        tags: ['Posts'],
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
                            'message' => 'This post doesn\'t belong to current user or not exist.',
                            'code' => 404,
                        ],
                    ],
                ),
            ),
        ],
    )]
    public function forceDelete(PostForceDeleteRequest $request): JsonResponse|Response
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
    #[OA\Post(
        path: '/v1/posts/{id}/soft-delete',
        summary: 'Soft delete post.',
        security: [['bearerAuth' => []]],
        tags: ['Posts'],
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
                            'message' => 'This post doesn\'t belong to current user or not exist.',
                            'code' => 404,
                        ],
                    ],
                ),
            ),
        ],
    )]
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
    #[OA\Post(
        path: '/v1/posts/{id}/restore',
        summary: 'Restore the soft deleted post.',
        security: [['bearerAuth' => []]],
        tags: ['Posts'],
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
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Post',
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
                    ref: '#/components/schemas/Error',
                    example: [
                        'data' => [
                            'message' => 'This post doesn\'t belong to current user or not soft deleted.',
                            'code' => 404,
                        ],
                    ],
                ),
            ),
        ],
    )]
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
    #[OA\Post(
        path: '/v1/posts/{id}/publish',
        summary: 'Publish a post.',
        security: [['bearerAuth' => []]],
        tags: ['Posts'],
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
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/Post',
                        ),
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: 'Error: Bad Request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Error',
                    example: [
                        'data' => [
                            'message' => 'This post is published already.',
                            'code' => 400,
                        ],
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
                    ref: '#/components/schemas/Error',
                    example: [
                        'data' => [
                            'message' => 'This post doesn\'t belong to current user or not soft deleted.',
                            'code' => 404,
                        ],
                    ],
                ),
            ),
        ],
    )]
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
    #[OA\Post(
        path: '/v1/posts/{id}/unpublish',
        summary: 'Unpublish a post.',
        security: [['bearerAuth' => []]],
        tags: ['Posts'],
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
                response: 400,
                description: 'Error: Bad Request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Error',
                    example: [
                        'data' => [
                            'message' => 'This post is published already.',
                            'code' => 400,
                        ],
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
                    ref: '#/components/schemas/Error',
                    example: [
                        'data' => [
                            'message' => 'This post doesn\'t belong to current user or not soft deleted.',
                            'code' => 404,
                        ],
                    ],
                ),
            ),
        ],
    )]
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
