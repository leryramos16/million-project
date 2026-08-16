<?php

namespace Leryr\Mymillionpesoproject\Repositories;

use PDO;

class AchievementRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT * FROM achievements ORDER BY requirement_value ASC')
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByRequirementType(string $type, int $achievedValue): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM achievements WHERE requirement_type = :type AND requirement_value <= :value'
        );
        $stmt->execute(['type' => $type, 'value' => $achievedValue]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasUnlocked(int $userId, int $achievementId): bool
    {
        $stmt = $this->db->prepare(
            'SELECT id FROM user_achievements WHERE user_id = :user_id AND achievement_id = :achievement_id LIMIT 1'
        );
        $stmt->execute(['user_id' => $userId, 'achievement_id' => $achievementId]);

        return (bool) $stmt->fetch();
    }

    public function unlock(int $userId, int $achievementId): void
    {
        $stmt = $this->db->prepare(
            'INSERT IGNORE INTO user_achievements (user_id, achievement_id) VALUES (:user_id, :achievement_id)'
        );
        $stmt->execute(['user_id' => $userId, 'achievement_id' => $achievementId]);
    }

    public function unlockedByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            'SELECT a.*, ua.unlocked_at
             FROM user_achievements ua
             JOIN achievements a ON a.id = ua.achievement_id
             WHERE ua.user_id = :user_id
             ORDER BY ua.unlocked_at DESC'
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
