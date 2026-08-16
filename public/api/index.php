<?php

date_default_timezone_set('Asia/Manila');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../app/core/config.php';

use Leryr\Mymillionpesoproject\Http\ApiRouter;
use Leryr\Mymillionpesoproject\Http\JsonResponse;

DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

set_exception_handler(function (Throwable $e) {
    if (DEBUG) {
        JsonResponse::error($e->getMessage(), 500, ['trace' => $e->getTraceAsString()]);
    } else {
        JsonResponse::error('Internal server error', 500);
    }
});

$path = $_GET['url'] ?? '';
$path = '/' . trim($path, '/');

(new ApiRouter($conn))->dispatch($_SERVER['REQUEST_METHOD'], $path);
