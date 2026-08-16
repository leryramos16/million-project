<?php

namespace Leryr\Mymillionpesoproject\Controllers\Api;

use Leryr\Mymillionpesoproject\Http\JsonResponse;
use Leryr\Mymillionpesoproject\Http\Request;
use Leryr\Mymillionpesoproject\Services\QuestService;

class QuestController
{
    public function __construct(private QuestService $quests)
    {
    }

    public function index(Request $request): void
    {
        $result = $this->quests->listApproved(
            $request->input('type'),
            (int) $request->input('page', 1),
            (int) $request->input('limit', 10)
        );

        JsonResponse::success($result);
    }

    public function show(Request $request, array $params): void
    {
        $quest = $this->quests->find((int) $params['id']);

        if (!$quest) {
            JsonResponse::error('Quest not found', 404);
        }

        JsonResponse::success($quest);
    }

    public function store(Request $request): void
    {
        $result = $this->quests->create($request->user['id'], $request->all(), $request->file('payment_proof'));

        if (!$result['success']) {
            JsonResponse::error($result['message'], 422);
        }

        JsonResponse::success(
            ['quest_id' => $result['quest_id']],
            'Quest submitted! Waiting for admin approval.',
            201
        );
    }

    public function accept(Request $request, array $params): void
    {
        $ok = $this->quests->accept((int) $params['id'], $request->user['id']);

        if (!$ok) {
            JsonResponse::error('Could not accept quest (it may be your own or already taken)', 422);
        }

        JsonResponse::success(null, 'Quest accepted! The contract is now bound.');
    }

    public function complete(Request $request, array $params): void
    {
        $result = $this->quests->complete((int) $params['id'], $request->user['id']);

        if (!$result['success']) {
            JsonResponse::error(
                'Failed. Quest may not belong to you, not accepted yet, or already completed.',
                422
            );
        }

        JsonResponse::success(
            ['new_achievements' => $result['new_achievements']],
            'Quest completed. XP and coins rewarded!'
        );
    }

    public function mine(Request $request): void
    {
        $result = $this->quests->myRequests(
            $request->user['id'],
            (string) $request->input('status', 'pending'),
            (int) $request->input('page', 1),
            (int) $request->input('limit', 10)
        );

        JsonResponse::success($result);
    }

    public function accepted(Request $request): void
    {
        JsonResponse::success($this->quests->acceptedByUser($request->user['id']));
    }
}
