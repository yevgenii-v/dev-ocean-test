<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class GuestMiddleware
{
    /**
     * Checks if the user is logged in.
     *
     * @param Closure(Request): (Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('api')->check() === true) {
            return response()->json(['message' => 'You are logged in already.'])
                ->setStatusCode(403);
        }

        return $next($request);
    }
}
