<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

final class AuthMiddleware
{
    const UnauthorizedErrorMessage = ['error' => ['message' => 'Unauthorized']];

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if ($request->isMethodSafe(false)) {
            return $next($request);
        }

        if ($this->containsValidAccessToken($request)) {
            return $next($request);
        }

        return response(self::UnauthorizedErrorMessage, 401);
    }

    public function containsValidAccessToken(Request $request)
    {
        $accessToken = $request->input('access_token');

        return $accessToken === env('APP_ACCESS_TOKEN', !$accessToken);
    }
}
