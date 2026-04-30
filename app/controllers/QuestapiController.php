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

            case 'addQuest':
                $this->addQuest();
                break;

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

    public function addQuest()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        echo json_encode([
            'status' => false,
            'message' => 'User not logged in'
        ]);
        return;
    }

    $title = $_POST['title'] ?? '';
    $description = $_POST['description'] ?? '';

    if (empty($title) || empty($description)) {
        echo json_encode([
            'status' => false,
            'message' => 'Title and description are required'
        ]);
        return;
    }

    if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] !== 0) {
        echo json_encode([
            'status' => false,
            'message' => 'Payment proof is required'
        ]);
        return;
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

    if (!in_array($_FILES['payment_proof']['type'], $allowedTypes)) {
        echo json_encode([
            'status' => false,
            'message' => 'Only JPG and PNG files are allowed'
        ]);
        return;
    }

    $uploadDir = 'public/uploads/payments/';

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    $fileName = time() . '_' . basename($_FILES['payment_proof']['name']);
    $targetPath = $uploadDir . $fileName;

    if (!move_uploaded_file($_FILES['payment_proof']['tmp_name'], $targetPath)) {
        echo json_encode([
            'status' => false,
            'message' => 'Failed to upload payment proof'
        ]);
        return;
    }

    $data = [
        'title' => $title,
        'description' => $description,
        'payment_proof' => $fileName,
        'status' => 'pending',
        'created_by' => $user_id
    ];

    $questModel = $this->model('Quests');
    $result = $questModel->create($data);

    echo json_encode([
        'status' => $result ? true : false,
        'message' => $result
            ? 'Quest submitted successfully! Waiting for admin approval.'
            : 'Failed to submit quest'
    ]);
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