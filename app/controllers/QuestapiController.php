<?php

class QuestapiController
{
    use Controller;

    public function index()
    {
        header('Content-Type: application/json');

        // 👉 HANDLE POST REQUEST (ACCEPT QUEST)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $method = $_POST['method'] ?? null;

            if ($method === 'acceptQuest') {
                $this->acceptQuest();
                return;
            }

            echo json_encode(["status" => false, "message" => "Invalid method"]);
            return;
        }

        // 👉 DEFAULT: GET ALL QUESTS
        $questModel = $this->model('Quests');
        $quests = $questModel->findAll();

        echo json_encode($quests);
    }

    private function acceptQuest()
    {
        $quest_id = $_POST['quest_id'] ?? null;

        if (!$quest_id) {
            echo json_encode(["status" => false, "message" => "No quest ID"]);
            return;
        }

        $questModel = $this->model('Quests');

        $result = $questModel->acceptQuest($quest_id);

        echo json_encode([
            "status" => $result,
            "message" => $result ? "Quest accepted" : "Failed"
        ]);
    }
}