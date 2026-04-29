<?php

class Quests
{
    private $db;
    protected $table = "quests";

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function create($data)
    {
        $sql = "INSERT INTO quests 
                (title, description, payment_proof, xp_reward, coins_reward, type, status, created_by)
                VALUES 
                (:title, :description, :payment_proof, :xp, :coins, :type, :status, :created_by)";

        $stmt = $this->db->prepare($sql);

        $result = $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'payment_proof' => $data['payment_proof'] ?? null,
            'xp' => $data['xp_reward'] ?? 0,
            'coins' => $data['coins_reward'] ?? 0,
            'type' => $data['type'] ?? 'side_quests',
            'status' => $data['status'] ?? 'pending',
            'created_by' =>$_SESSION['user_id'] ?? null
        ]);

        return $result ? $this->db->lastInsertId() : false;
    }

    public function findAll()
    {
        $sql = "SELECT * FROM quests WHERE status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPendingRequests()
    {
        $sql = "SELECT * FROM quests WHERE status = 'pending'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findQuestById($id)
    {
        $sql = "SELECT * FROM quests WHERE id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'id' => $id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateQuest($data)
    {
        $sql = "UPDATE quests
                SET title = :title,
                    description = :description,
                    xp_reward = :xp_reward,
                    coins_reward = :coins_reward,
                    type = :type
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'xp_reward' => $data['xp_reward'],
            'coins_reward' => $data['coins_reward'],
            'type' => $data['type'],
            'id' => $data['id']
        ]);
    }

    public function publishQuest($id)
    {
        $sql = "UPDATE quests
                SET status = 'approved'
                WHERE id = :id";

        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            'id' => $id
        ]);
    }

    public function acceptQuest($quest_id, $user_id)
    {
        $sql = "UPDATE quests
                SET status = 'accepted',
                    accepted_by = :user_id
                WHERE id = :quest_id
                AND status = 'approved'
                AND created_by != :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'quest_id' => $quest_id,
            'user_id'  => $user_id
        ]);

        return $stmt->rowCount() > 0;
    }

    public function getAcceptedQuestsByUser($user_id)
{
    $sql = "SELECT * FROM quests
            WHERE accepted_by = :user_id
            AND status = 'accepted'
            ORDER BY created_at DESC";

    $stmt = $this->db->prepare($sql);
    $stmt->execute(['user_id' => $user_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function completeQuest($quest_id, $user_id)
{
    $this->db->beginTransaction();

    try {
        $questSql = "SELECT * FROM quests
                     WHERE id = :quest_id
                     AND accepted_by = :user_id
                     AND status = 'accepted'
                     LIMIT 1";

        $stmt = $this->db->prepare($questSql);
        $stmt->execute([
            'quest_id' => $quest_id,
            'user_id' => $user_id
        ]);

        $quest = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$quest) {
            $this->db->rollBack();
            return false;
        }

        $xpReward = (int) $quest['xp_reward'];
        $coinReward = (int) $quest['coins_reward'];

        $userSql = "SELECT id, level, xp, coins
                    FROM users
                    WHERE id = :user_id
                    LIMIT 1";

        $stmt = $this->db->prepare($userSql);
        $stmt->execute([
            'user_id' => $user_id
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->db->rollBack();
            return false;
        }

        $newXp = (int) $user['xp'] + $xpReward;
        $newCoins = (int) $user['coins'] + $coinReward;
        $newLevel = (int) $user['level'];

        while ($newXp >= ($newLevel * 100)) {
            $newXp -= ($newLevel * 100);
            $newLevel++;
        }

        $updateUserSql = "UPDATE users
                          SET xp = :xp,
                              coins = :coins,
                              level = :level
                          WHERE id = :user_id";

        $stmt = $this->db->prepare($updateUserSql);
        $stmt->execute([
            'xp' => $newXp,
            'coins' => $newCoins,
            'level' => $newLevel,
            'user_id' => $user_id
        ]);

        $updateQuestSql = "UPDATE quests
                           SET status = 'completed'
                           WHERE id = :quest_id";

        $stmt = $this->db->prepare($updateQuestSql);
        $stmt->execute([
            'quest_id' => $quest_id
        ]);

        $this->db->commit();

        return true;

    } catch (Exception $e) {
        $this->db->rollBack();
        return false;
    }
}

    public function getMyRequests($user_id)
    {
        $sql = "SELECT 
                    q.*,
                    u.username AS accepted_by_name
                FROM quests q
                LEFT JOIN users u
                    ON q.accepted_by = u.id
                WHERE q.created_by = :user_id
                ORDER BY q.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id
        ]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function markQuestDone($quest_id, $owner_id)
{
    $this->db->beginTransaction();

    try {
        $sql = "SELECT *
                FROM quests
                WHERE id = :quest_id
                AND created_by = :owner_id
                AND status = 'accepted'
                AND accepted_by IS NOT NULL
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'quest_id' => $quest_id,
            'owner_id' => $owner_id
        ]);

        $quest = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$quest) {
            $this->db->rollBack();
            return false;
        }

        $acceptedUserId = $quest['accepted_by'];
        $xpReward = (int) $quest['xp_reward'];
        $coinsReward = (int) $quest['coins_reward'];

        // Get accepted user's current stats
        $sql = "SELECT id, level, xp, coins
                FROM users
                WHERE id = :user_id
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $acceptedUserId
        ]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            $this->db->rollBack();
            return false;
        }

        $newXp = (int)$user['xp'] + $xpReward;
        $newCoins = (int)$user['coins'] + $coinsReward;
        $newLevel = (int)$user['level'];

        // Level up logic: Level 1 needs 100 XP, Level 2 needs 200 XP, etc.
        while ($newXp >= ($newLevel * 100)) {
            $newXp -= ($newLevel * 100);
            $newLevel++;
        }

        // Update accepted user's rewards
        $sql = "UPDATE users
                SET xp = :xp,
                    coins = :coins,
                    level = :level
                WHERE id = :user_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'xp' => $newXp,
            'coins' => $newCoins,
            'level' => $newLevel,
            'user_id' => $acceptedUserId
        ]);

        // Mark quest as completed
        $sql = "UPDATE quests
                SET status = 'completed'
                WHERE id = :quest_id";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'quest_id' => $quest_id
        ]);

        $this->db->commit();
        return true;

    } catch (Exception $e) {
        $this->db->rollBack();
        return false;
    }
}

    public function getUserStats($user_id)
    {
        $sql = "SELECT id, username, level, xp, coins
                FROM users
                WHERE id = :user_id
                LIMIT 1";
                
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'user_id' => $user_id
        ]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}