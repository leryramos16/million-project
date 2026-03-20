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
        $sql = "INSERT INTO quests (title, description, payment_proof, xp_reward, coins_reward, type)
                VALUES (:title, :description, :payment_proof, :xp, :coins, :type)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'payment_proof' => $data['payment_proof'],
            'xp' => $data['xp_reward'],
            'coins' => $data['coins_reward'],
            'type' => $data['type']
            
        ]);
    }

        public function getAllQuests()
    {
        $sql = "SELECT * FROM quests WHERE status = 'approved'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // get quests for admin
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
}

