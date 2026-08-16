<?php

namespace Leryr\Mymillionpesoproject\Http\Middleware;

use Leryr\Mymillionpesoproject\Http\JsonResponse;
use Leryr\Mymillionpesoproject\Http\Request;
use Leryr\Mymillionpesoproject\Support\Jwt;

class AuthMiddleware
{
    public static function authenticate(Request $request): void
    {
        $token = $request->bearerToken();

        if (!$token) {
            JsonResponse::error('Authentication required', 401);
        }

        $payload = Jwt::decode($token);

        if (!$payload) {
            JsonResponse::error('Invalid or expired token', 401);
        }

        $request->user = [
            'id' => (int) $payload['sub'],
            'role' => $payload['role'],
        ];
    }
}
