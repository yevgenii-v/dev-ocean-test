<?php

namespace App\Http\Middleware\API;

use App\Enums\RoleEnum;
use App\Models\User;
use App\Services\User\UserAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUserAdmin
{
    public function __construct(
        protected UserAuthService $userAuthService,
    ) {
    }

    /**
     * Handle an incoming request.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $roleCheck = $this->userAuthService->isUserHasRole(RoleEnum::Administrator);

        if ($roleCheck === false) {
            return response()
                ->json(['message' => 'Permission denied.'])
                ->setStatusCode(403);
        }

        return $next($request);
    }
}
