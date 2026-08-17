<?php

namespace Leryr\Mymillionpesoproject\Repositories;

use PDO;

class UserRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByUsername(string $username): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute(['email' => $email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function findByUsernameOrEmail(string $usernameOrEmail): ?array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE username = :value OR email = :value LIMIT 1'
        );
        $stmt->execute(['value' => $usernameOrEmail]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function create(string $username, string $email, string $hashedPassword): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO users (username, email, password) VALUES (:username, :email, :password)'
        );
        $stmt->execute([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function updateStats(int $id, int $xp, int $coins, int $level): void
    {
        $stmt = $this->db->prepare(
            'UPDATE users SET xp = :xp, coins = :coins, level = :level WHERE id = :id'
        );
        $stmt->execute(['xp' => $xp, 'coins' => $coins, 'level' => $level, 'id' => $id]);
    }

    /** Atomically adjusts a user's coin balance (positive to add, negative to deduct). */
    public function adjustCoins(int $id, int $delta): void
    {
        $stmt = $this->db->prepare('UPDATE users SET coins = coins + :delta WHERE id = :id');
        $stmt->execute(['delta' => $delta, 'id' => $id]);
    }

    public function sumCoins(): int
    {
        return (int) $this->db->query("SELECT COALESCE(SUM(coins), 0) FROM users WHERE role != 'admin'")
            ->fetchColumn();
    }

    public function updateProfileImage(int $id, string $filename): void
    {
        $stmt = $this->db->prepare('UPDATE users SET profile_image = :image WHERE id = :id');
        $stmt->execute(['image' => $filename, 'id' => $id]);
    }

    public function all(): array
    {
        return $this->db->query(
            'SELECT id, username, email, role, level, xp, coins, profile_image, created_at FROM users ORDER BY id DESC'
        )->fetchAll(PDO::FETCH_ASSOC);
    }

    public function count(): int
    {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function topPlayers(int $limit): array
    {
        $stmt = $this->db->prepare(
            'SELECT id, username, level, xp, coins, profile_image
             FROM users
             WHERE role != \'admin\'
             ORDER BY level DESC, xp DESC
             LIMIT :limit'
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
