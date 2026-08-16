<?php

namespace Leryr\Mymillionpesoproject\Controllers\Api;

use Leryr\Mymillionpesoproject\Http\JsonResponse;
use Leryr\Mymillionpesoproject\Http\Request;
use Leryr\Mymillionpesoproject\Repositories\UserRepository;
use Leryr\Mymillionpesoproject\Services\QuestService;

class AdminController
{
    public function __construct(private QuestService $quests, private UserRepository $users)
    {
    }

    public function pendingQuests(Request $request): void
    {
        JsonResponse::success($this->quests->adminPending(
            (int) $request->input('page', 1),
            (int) $request->input('limit', 10)
        ));
    }

    public function updateQuest(Request $request, array $params): void
    {
        $ok = $this->quests->adminUpdate((int) $params['id'], $request->all());

        if (!$ok) {
            JsonResponse::error('Failed to update quest', 422);
        }

        JsonResponse::success(null, 'Quest updated');
    }

    public function publishQuest(Request $request, array $params): void
    {
        $ok = $this->quests->adminPublish((int) $params['id']);

        if (!$ok) {
            JsonResponse::error('Failed to publish quest', 422);
        }

        JsonResponse::success(null, 'Quest published successfully!');
    }

    public function stats(Request $request): void
    {
        JsonResponse::success($this->quests->adminStats());
    }

    public function users(Request $request): void
    {
        JsonResponse::success($this->users->all());
    }
}
