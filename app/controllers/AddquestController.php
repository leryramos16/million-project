<?php

class AddquestController
{
    use Controller;

    public function index()
    {
        Auth::requireLogin();
        //check kung meron ng current session state/start pag wala pa
        if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/login");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $questModel = new Quests();

            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'status' => 'pending'
            ];

            $questModel->create($data);
            header("Location: " . ROOT . "/addquest");
            exit;
        }

        $this->view('addquest');
    }
}