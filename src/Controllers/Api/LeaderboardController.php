<?php

namespace Leryr\Mymillionpesoproject\Controllers\Api;

use Leryr\Mymillionpesoproject\Http\JsonResponse;
use Leryr\Mymillionpesoproject\Http\Request;
use Leryr\Mymillionpesoproject\Repositories\UserRepository;

class LeaderboardController
{
    public function __construct(private UserRepository $users)
    {
    }

    public function index(Request $request): void
    {
        $limit = (int) $request->input('limit', 20);
        JsonResponse::success($this->users->topPlayers(max(1, min($limit, 100))));
    }
}
