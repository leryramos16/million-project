<?php

namespace Leryr\Mymillionpesoproject\Repositories;

use PDO;

class AuthTokenRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function store(int $userId, string $tokenHash, int $ttlSeconds): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO auth_tokens (user_id, token_hash, expires_at)
             VALUES (:user_id, :token_hash, DATE_ADD(NOW(), INTERVAL :ttl SECOND))'
        );
        $stmt->execute([
            'user_id' => $userId,
            'token_hash' => $tokenHash,
            'ttl' => $ttlSeconds,
        ]);
    }

    public function findValid(string $tokenHash): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM auth_tokens
             WHERE token_hash = :token_hash
             AND revoked_at IS NULL
             AND expires_at > NOW()
             LIMIT 1'
        );
        $stmt->execute(['token_hash' => $tokenHash]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function revoke(string $tokenHash): void
    {
        $stmt = $this->db->prepare('UPDATE auth_tokens SET revoked_at = NOW() WHERE token_hash = :token_hash');
        $stmt->execute(['token_hash' => $tokenHash]);
    }
}
