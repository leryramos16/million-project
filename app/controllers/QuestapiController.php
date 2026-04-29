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

            case 'getMyRequests':
                $this->getMyRequests();
                break;
            
            case 'markQuestDone':
                $this->markQuestDone();
                break;

            case 'getUserStats':
                $this->getUserStats();
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
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$quest_id) {
            echo json_encode([
                "status" => false,
                "message" => "No quest ID"
            ]);
            return;
        }

        if (!$user_id) {
            echo json_encode([
                "status" => false,
                "message" => "User not logged in"
            ]);
            return;
        }

        $questModel = $this->model('Quests');
        $result = $questModel->acceptQuest($quest_id, $user_id);

        echo json_encode([
            "status" => $result ? true : false,
            "message" => $result ? "Quest accepted" : "Failed to accept quest/ own quest"
        ]);
    }

    public function getMyRequests()
    {
        $user_id = $_SESSION['user_id'] ?? null;
        
        if (!$user_id) {
            echo json_encode([
                "status" => false,
                "message" => "User not logged in"
            ]);
            return;
        }

        $questModel = $this->model('Quests');
        $requests = $questModel->getMyRequests($user_id);
        
        echo json_encode([
            "status" => true,
            "data" => $requests
        ]);
    }

    public function markQuestDone()
    {
        $quest_id = $_POST['quest_id'] ?? null;
        $user_id = $_SESSION['user_id'] ?? null;

        if (!$quest_id) {
            echo json_encode([
                "status" => false,
                "message" => "No quest ID"
            ]);
            return;
        }

        if (!$user_id) {
            echo json_encode([
                "status" => false,
                "message" => "User not logged in"
            ]);
            return;
        }

        $questModel = $this->model('Quests');
        $result = $questModel->markQuestDone($quest_id, $user_id);

        echo json_encode([
            "status" => $result,
            "message" => $result 
                ? "Quest completed. XP and coins rewarded!" 
                : "Failed. Quest may not belong to you, not accepted yet, or already completed."
        ]);
    }

    public function getUserStats()
{
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode([
            "status" => false,
            "message" => "User not logged in"
        ]);
        return;
    }

    $questModel = $this->model('Quests');
    $user = $questModel->getUserStats($user_id);

    echo json_encode([
        "status" => $user ? true : false,
        "data" => $user
    ]);
}

}