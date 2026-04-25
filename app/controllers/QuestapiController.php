<?php 

class QuestApiController {

    use Controller;

    public function index()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handlePostRequest();
            return;
        }

        $this->getAllQuests();
    }

    private function handlePostRequest()
    {
        $method = $_POST['method'] ?? '';

        switch ($method) {
            case 'acceptQuest':
                $this->acceptQuest();
                break;

            default:
                echo json_encode([
                    "status" => false,
                    "message" => "Invalid method"
                ]);
                break;
        }
    }

    public function getAllQuests()
    {
        $questModel = $this->model('Quests');
        $quests = $questModel->findAll();

        echo json_encode($quests);
    }


    public function acceptQuest()
    {
        $quest_id = $_POST['quest_id'] ?? null;

        if (!$quest_id) {
            echo json_encode([
                "status" => false,
                "message" => "No quest ID"
            ]);
            return;
        }

        $questModel = $this->model('Quests');
        $result = $questModel->acceptQuest($quest_id);

        echo json_encode([
            "status" => $result ? true : false,
            "message" => $result ? "Quest accepted" : "Failed to accept quest"
        ]);
    }

}