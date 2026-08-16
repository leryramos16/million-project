<?php

namespace Leryr\Mymillionpesoproject\Http;

class JsonResponse
{
    public static function success($data = null, string $message = '', int $status = 200): void
    {
        self::send([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    public static function error(string $message, int $status = 400, array $errors = []): void
    {
        self::send([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $status);
    }

    private static function send(array $payload, int $status): void
    {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }
}
