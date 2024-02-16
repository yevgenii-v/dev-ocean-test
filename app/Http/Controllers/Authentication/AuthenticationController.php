<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\UserRegisterRequest;
use App\Http\Resources\ExceptionResource;
use App\Http\Resources\UserResource;
use App\Repositories\User\UserCreateDTO;
use App\Services\User\Login\LoginDTO;
use App\Services\User\Login\LoginService;
use App\Services\User\UserAuthService;
use App\Services\User\UserService;
use Exception;
use Illuminate\Http\JsonResponse;

class AuthenticationController extends Controller
{
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
    public function profile(): JsonResponse
    {
        $userIterator = $this->userAuthService->getUserByBearerToken();

        $resource = new UserResource($userIterator);

        return $resource->response()->setStatusCode(200);
    }

    /**
     * @return JsonResponse
     */
    public function logout(): JsonResponse
    {
        $token = $this->userAuthService->getUserToken();
        $token->revoke();

        return response()->json(['message' => 'User was logged out.'])
            ->setStatusCode(200);
    }
}
