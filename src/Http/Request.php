<?php

namespace Leryr\Mymillionpesoproject\Http;

class Request
{
    private array $body;
    public ?array $user = null; // set by AuthMiddleware: ['id' => int, 'role' => string]

    public function __construct()
    {
        $this->body = $this->parseBody();
    }

    private function parseBody(): array
    {
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';

        if (str_contains($contentType, 'application/json')) {
            $raw = file_get_contents('php://input');
            $decoded = json_decode($raw, true);
            return is_array($decoded) ? $decoded : [];
        }

        // multipart/form-data and application/x-www-form-urlencoded both land in $_POST
        return $_POST;
    }

    public function input(string $key, $default = null)
    {
        return $this->body[$key] ?? $_GET[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($_GET, $this->body);
    }

    public function file(string $key): ?array
    {
        if (!isset($_FILES[$key]) || $_FILES[$key]['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        return $_FILES[$key];
    }

    public function bearerToken(): ?string
    {
        $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';

        if (!$header && function_exists('getallheaders')) {
            // Header name casing isn't guaranteed by clients (Dart's HTTP client lowercases it),
            // and PHP array keys are case-sensitive, so match case-insensitively.
            foreach (getallheaders() as $name => $value) {
                if (strcasecmp($name, 'Authorization') === 0) {
                    $header = $value;
                    break;
                }
            }
        }

        if (preg_match('/Bearer\s+(.*)$/i', $header, $matches)) {
            return $matches[1];
        }

        return null;
    }
}
