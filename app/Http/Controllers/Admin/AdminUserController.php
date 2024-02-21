<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserBanRequest;
use App\Http\Requests\Admin\UserRestoreRequest;
use App\Http\Resources\Admin\AdminUserResource;
use App\Http\Resources\Errors\ExceptionResource;
use App\Services\User\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use OpenApi\Attributes as OA;

class AdminUserController extends Controller
{
    /**
     * @param UserService $userService
     */
    public function __construct(
        protected UserService $userService,
    ) {
    }

    /**
     * Ban a user.
     *
     * @param UserBanRequest $request
     * @return Response|JsonResponse
     */
    #[OA\Post(
        path: '/v1/admin/users/{user}/ban',
        summary: 'To ban the user.',
        security: [['bearerAuth' => []]],
        tags: ['Admin Panel'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
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
                    examples: [
                        new OA\Examples(
                            example: 'Self ban',
                            summary: '',
                            description: '',
                            value: [
                                'message'   => 'You can\'t ban yourself!',
                                'code'      => 400,
                            ],
                        ),
                        new OA\Examples(
                            example: 'User is banned',
                            summary: '',
                            description: '',
                            value: [
                                'message'   => 'This user is already banned.',
                                'code'      => 400,
                            ],
                        ),
                    ],
                    ref: '#/components/schemas/Error',
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
                description: 'The user doesn\'t have permission.',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/Error',
                    example: [
                        'message'   => 'Permission Denied.',
                        'code'      => 403,
                    ],
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
    public function ban(UserBanRequest $request): Response|JsonResponse
    {
        try {
            $validated = $request->validated();
            $this->userService->ban($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        return response()->noContent();
    }

    /**
     * Unban a user.
     *
     * @param UserRestoreRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/v1/admin/users/{user}/restore',
        summary: 'To unban the user.',
        security: [['bearerAuth' => []]],
        tags: ['Admin Panel'],
        parameters: [
            new OA\Parameter(
                name: 'id',
                description: 'User ID',
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
                            ref: '#/components/schemas/AdminUser',
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
                        'message'   => 'This user isn\'t banned.',
                        'code'      => 400
                    ],
                ),
            ),            new OA\Response(
                response: 401,
                description: 'Error: Unauthorized',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message' => 'Unauthorized.'],
                ),
            ),
            new OA\Response(
                response: 403,
                description: 'Error: Forbidden',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message'   => 'Permission Denied.'],
                ),
            ),
        ],
    )]
    public function restore(UserRestoreRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();
            $service = $this->userService->restore($validated['id']);
        } catch (Exception $e) {
            return (new ExceptionResource($e))
                ->response()
                ->setStatusCode($e->getCode());
        }

        $resource = new AdminUserResource($service);

        return $resource->response()->setStatusCode(200);
    }
}
