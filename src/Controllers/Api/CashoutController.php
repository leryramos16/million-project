<?php

namespace Leryr\Mymillionpesoproject\Controllers\Api;

use Leryr\Mymillionpesoproject\Http\JsonResponse;
use Leryr\Mymillionpesoproject\Http\Request;
use Leryr\Mymillionpesoproject\Services\CashoutService;

class CashoutController
{
    public function __construct(private CashoutService $cashouts)
    {
    }

    public function store(Request $request): void
    {
        $result = $this->cashouts->request($request->user['id'], $request->all());

        if (!$result['success']) {
            JsonResponse::error($result['message'], 422);
        }

        JsonResponse::success(['id' => $result['id']], 'Cashout requested! Paid out on the next payout run.', 201);
    }

    public function history(Request $request): void
    {
        JsonResponse::success($this->cashouts->history($request->user['id']));
    }
}
