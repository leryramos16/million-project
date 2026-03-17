<?php

class AdminController {

use Controller;

    public function index()
    {   
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        Auth::requireLogin();
        Auth::requireAdmin();
        
        $this->view('admin/dashboard');
    }

    public function viewPendingRequests()
    {
        $questModel = new Quests;
        $questModel->getPendingRequests();
        $data['quests'] = $questModel->getPendingRequests();

        $this->view('admin/pendingquest', $data);
    }

    public function editQuest($id)
    {
        $questModel = new Quests();

        if ($_SERVER['REQUEST_METHOD'] == "POST") {
            $data = [
                'id' => $id,
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'xp_reward' => $_POST['xp_reward'],
                'coins_reward' => $_POST['coins_reward'],
                'type' => $_POST['type']
            ];

            $questModel->updateQuest($data);

            header("Location: " . ROOT . "/admin/viewPendingRequests");
            exit;
        }
            $data['quest'] = $questModel->findQuestById($id);
            $this->view('admin/editquest', $data);
        
    }

    public function publishQuest($id)
    {
        $questModel = new Quests();

        $questModel->publishQuest($id);
        $_SESSION['success'] = "Quest published successfully!";
        header("Location: " . ROOT . "/admin/viewPendingRequests");

    }
    
}