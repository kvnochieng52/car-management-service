<?php

namespace App\Http\Middleware;

use Closure;
use App\Services\UserService;
use Illuminate\Http\Request;

class ValidateTokenMiddleware
{
    protected $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $user = $this->userService->validateToken($token);

        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Attach user data to request if needed
        $request->merge(['authenticatedUser' => $user]);

        return $next($request);
    }
}
