<?php

namespace App\Http\Middleware\API;

use App\Services\User\UserAuthService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsUserBanned
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
        if ($this->userAuthService->isUserBanned() === true) {
            return response()->json(['message' => 'You has been banned.']);
        }

        return $next($request);
    }
}
