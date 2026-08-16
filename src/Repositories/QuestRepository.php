<?php

namespace Leryr\Mymillionpesoproject\Repositories;

use PDO;

class QuestRepository
{
    public function __construct(private PDO $db)
    {
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO quests (title, description, payment_proof, xp_reward, coins_reward, type, difficulty, status, created_by)
             VALUES (:title, :description, :payment_proof, :xp, :coins, :type, :difficulty, :status, :created_by)'
        );

        $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'payment_proof' => $data['payment_proof'] ?? null,
            'xp' => $data['xp_reward'] ?? 0,
            'coins' => $data['coins_reward'] ?? 0,
            'type' => $data['type'] ?? 'side_quests',
            'difficulty' => $data['difficulty'] ?? 'easy',
            'status' => $data['status'] ?? 'pending',
            'created_by' => $data['created_by'],
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findApproved(?string $type, int $limit, int $offset): array
    {
        $sql = "SELECT q.*, u.username FROM quests q
                LEFT JOIN users u ON u.id = q.created_by
                WHERE q.status = 'approved'";

        $params = [];

        if ($type) {
            $sql .= ' AND q.type = :type';
            $params['type'] = $type;
        }

        $sql .= ' ORDER BY q.created_at DESC LIMIT :limit OFFSET :offset';

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue(':' . $key, $value);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countApproved(?string $type): int
    {
        $sql = "SELECT COUNT(*) FROM quests WHERE status = 'approved'";
        $params = [];

        if ($type) {
            $sql .= ' AND type = :type';
            $params['type'] = $type;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return (int) $stmt->fetchColumn();
    }

    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT q.*, u.username FROM quests q
             LEFT JOIN users u ON u.id = q.created_by
             WHERE q.id = :id LIMIT 1"
        );
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function update(array $data): bool
    {
        $stmt = $this->db->prepare(
            'UPDATE quests
             SET title = :title, description = :description, xp_reward = :xp_reward,
                 coins_reward = :coins_reward, type = :type, difficulty = :difficulty
             WHERE id = :id'
        );

        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'xp_reward' => $data['xp_reward'],
            'coins_reward' => $data['coins_reward'],
            'type' => $data['type'],
            'difficulty' => $data['difficulty'] ?? 'easy',
            'id' => $data['id'],
        ]);
    }

    public function publish(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE quests SET status = 'approved' WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function accept(int $questId, int $userId): bool
    {
        $stmt = $this->db->prepare(
            "UPDATE quests SET status = 'accepted', accepted_by = :user_id
             WHERE id = :quest_id AND status = 'approved' AND created_by != :user_id"
        );
        $stmt->execute(['quest_id' => $questId, 'user_id' => $userId]);

        return $stmt->rowCount() > 0;
    }

    public function findAcceptedForCompletion(int $questId, int $ownerId): ?array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM quests
             WHERE id = :quest_id AND created_by = :owner_id
             AND status = 'accepted' AND accepted_by IS NOT NULL
             LIMIT 1"
        );
        $stmt->execute(['quest_id' => $questId, 'owner_id' => $ownerId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return $row ?: null;
    }

    public function markCompleted(int $questId): void
    {
        $stmt = $this->db->prepare("UPDATE quests SET status = 'completed' WHERE id = :id");
        $stmt->execute(['id' => $questId]);
    }

    public function getAcceptedByUser(int $userId): array
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM quests WHERE accepted_by = :user_id AND status = 'accepted'
             ORDER BY created_at DESC"
        );
        $stmt->execute(['user_id' => $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getMyRequests(int $userId, string $status, int $limit, int $offset): array
    {
        $stmt = $this->db->prepare(
            "SELECT q.*, u.username AS accepted_by_name
             FROM quests q
             LEFT JOIN users u ON q.accepted_by = u.id
             WHERE q.created_by = :user_id AND q.status = :status
             ORDER BY q.created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countMyRequests(int $userId, string $status): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM quests WHERE created_by = :user_id AND status = :status'
        );
        $stmt->execute(['user_id' => $userId, 'status' => $status]);

        return (int) $stmt->fetchColumn();
    }

    public function getPending(int $limit, int $offset): array
    {
        $stmt = $this->db->prepare(
            "SELECT q.*, u.username FROM quests q
             LEFT JOIN users u ON u.id = q.created_by
             WHERE q.status = 'pending'
             ORDER BY q.created_at DESC LIMIT :limit OFFSET :offset"
        );
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countPending(): int
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM quests WHERE status = 'pending'")->fetchColumn();
    }

    public function countCompletedByUser(int $userId): int
    {
        $stmt = $this->db->prepare(
            "SELECT COUNT(*) FROM quests WHERE accepted_by = :user_id AND status = 'completed'"
        );
        $stmt->execute(['user_id' => $userId]);

        return (int) $stmt->fetchColumn();
    }

    public function countCompletedToday(): int
    {
        return (int) $this->db->query(
            "SELECT COUNT(*) FROM quests WHERE status = 'completed' AND DATE(created_at) = CURDATE()"
        )->fetchColumn();
    }
}
