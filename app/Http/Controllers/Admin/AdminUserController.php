<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserBanRequest;
use App\Http\Requests\Admin\UserRestoreRequest;
use App\Http\Resources\Admin\AdminUserResource;
use App\Http\Resources\ExceptionResource;
use App\Services\User\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

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
