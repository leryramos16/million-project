<?php

class Quests
{
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->connect();
    }

    public function create($data)
    {
        $sql = "INSERT INTO quests (title, description, xp_reward, coins_reward, type)
                VALUES (:title, :description, :xp, :coins, :type)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'title' => $data['title'],
            'description' => $data['description'],
            'xp' => $data['xp_reward'],
            'coins' => $data['coins_reward'],
            'type' => $data['type']
            
        ]);
    }

        public function getAllQuests()
    {
        $sql = "SELECT * FROM quests";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

