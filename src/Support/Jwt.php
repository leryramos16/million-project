<?php

namespace Leryr\Mymillionpesoproject\Support;

use Firebase\JWT\JWT as FirebaseJWT;
use Firebase\JWT\Key;

class Jwt
{
    private const ALGO = 'HS256';
    private const ACCESS_TTL = 3600;        // 1 hour
    public const REFRESH_TTL = 2592000;     // 30 days

    private static function secret(): string
    {
        // Fixed dev secret so tokens survive server restarts; move to an env var for production.
        return 'quest-app-' . DBNAME . '-secret-key';
    }

    public static function issueAccessToken(int $userId, string $role): string
    {
        $now = time();

        return FirebaseJWT::encode([
            'sub' => $userId,
            'role' => $role,
            'iat' => $now,
            'exp' => $now + self::ACCESS_TTL,
        ], self::secret(), self::ALGO);
    }

    public static function decode(string $token): ?array
    {
        try {
            $decoded = FirebaseJWT::decode($token, new Key(self::secret(), self::ALGO));
            return (array) $decoded;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public static function newRefreshToken(): string
    {
        return bin2hex(random_bytes(40));
    }

    public static function hashRefreshToken(string $token): string
    {
        return hash('sha256', $token);
    }
}
