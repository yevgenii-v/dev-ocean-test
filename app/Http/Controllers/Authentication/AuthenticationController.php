<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\UserRegisterRequest;
use App\Http\Resources\Errors\ExceptionResource;
use App\Http\Resources\UserResource;
use App\Repositories\User\UserCreateDTO;
use App\Services\User\Login\LoginDTO;
use App\Services\User\Login\LoginService;
use App\Services\User\UserAuthService;
use App\Services\User\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class AuthenticationController extends Controller
{
    /**
     * @param UserService $userService
     * @param LoginService $loginService
     * @param UserAuthService $userAuthService
     */
    public function __construct(
        protected UserService $userService,
        protected LoginService $loginService,
        protected UserAuthService $userAuthService,
    ) {
    }

    /**
     * @param UserRegisterRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/v1/register',
        summary: 'Create a new user.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            ref: '#/components/requestBodies/UserRegisterRequest',
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 201,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/User',
                        ),
                    ],
                ),
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message' => 'You are logged in already.'],
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
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $DTO = new UserCreateDTO(...$validated);

        $userService = $this->userService->register($DTO);
        $resource = new UserResource($userService);

        return $resource->response()->setStatusCode(201);
    }

    /**
     * @param LoginRequest $request
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/v1/login',
        summary: 'Enter to your account.',
        security: [['bearerAuth' => []]],
        requestBody: new OA\RequestBody(
            ref: '#/components/requestBodies/LoginRequest',
        ),
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'OK',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/User',
                        ),
                        new OA\Property(
                            property: 'Bearer',
                            ref: '#/components/schemas/Bearer'
                        ),
                    ],
                ),
            ),
            new OA\Response(
                response: 400,
                description: 'Error: Bad Request',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message' => 'Credentials do not match.'],
                ),
            ),
            new OA\Response(
                response: 403,
                description: 'Forbidden',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message' => 'You are logged in already.'],
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
    public function login(LoginRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $DTO = new LoginDTO(...$validated);

        try {
            $service = $this->loginService->handle($DTO);
        } catch (Exception $e) {
            $errorResource = new ExceptionResource($e);
            return $errorResource->response()
                ->setStatusCode(400);
        }

        $resource = new UserResource($service->getUserIterator());

        return $resource->additional([
            'Bearer' => $DTO->getBearerToken(),
        ])
            ->response()
            ->setStatusCode(200);
    }

    /**
     * @return JsonResponse
     */
    #[OA\Get(
        path: '/v1/profile',
        summary: 'Shows your profile.',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Shows user profile',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            property: 'data',
                            ref: '#/components/schemas/User',
                        )
                    ],
                ),
            ),
            new OA\Response(
                response: 401,
                description: 'Error: Unauthorized',
                content: new OA\JsonContent(
                    example: ['message' => 'Unauthenticated.']
                )
            ),
        ],
    )]
    public function profile(): JsonResponse
    {
        $userIterator = $this->userAuthService->getUserByBearerToken();

        $resource = new UserResource($userIterator);

        return $resource->response()->setStatusCode(200);
    }

    /**
     * @return JsonResponse
     */
    #[OA\Post(
        path: '/v1/logout',
        summary: 'Logout and revoke user token.',
        security: [['bearerAuth' => []]],
        tags: ['Authentication'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Logout user',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message' => 'User was logged out.'],
                )
            ),
            new OA\Response(
                response: 401,
                description: 'Error: Unauthorized',
                content: new OA\JsonContent(
                    ref: '#/components/schemas/MiddlewareError',
                    example: ['message' => 'Unauthenticated.']
                )
            ),
        ],
    )]
    public function logout(): JsonResponse
    {
        $token = $this->userAuthService->getUserToken();
        $token->revoke();

        return response()->json(['message' => 'User was logged out.'])
            ->setStatusCode(200);
    }
}
