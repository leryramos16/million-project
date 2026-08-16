<?php

namespace Leryr\Mymillionpesoproject\Http\Middleware;

use Leryr\Mymillionpesoproject\Http\JsonResponse;
use Leryr\Mymillionpesoproject\Http\Request;

class AdminMiddleware
{
    public static function requireAdmin(Request $request): void
    {
        AuthMiddleware::authenticate($request);

        if (($request->user['role'] ?? null) !== 'admin') {
            JsonResponse::error('Admin access required', 403);
        }
    }
}
