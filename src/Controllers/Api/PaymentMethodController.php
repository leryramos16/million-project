<?php

namespace Leryr\Mymillionpesoproject\Controllers\Api;

use Leryr\Mymillionpesoproject\Http\JsonResponse;
use Leryr\Mymillionpesoproject\Http\Request;
use Leryr\Mymillionpesoproject\Services\PaymentMethodService;

class PaymentMethodController
{
    public function __construct(private PaymentMethodService $service)
    {
    }

    public function index(Request $request): void
    {
        JsonResponse::success($this->service->listActive());
    }
}
