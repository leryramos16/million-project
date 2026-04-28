<?php


class QuestsController
{
    use Controller;

    public function index()
    {
        Auth::requireAdmin();
         if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }



        $this->view('/admin/dashboard');
    }

    public function create()
    {   
        // Initial data for form and errors
        $data = [
            'title' => '',
            'description' => '',
            'xp_reward' => 0,
            'coins_reward' => 0,
            'type' => 'main_quests',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Trim inputs
            $title = trim($_POST['title']);
            $description = trim($_POST['description']);
            $xp = (int) $_POST['xp_reward'];
            $coins = (int) $_POST['coins_reward'];
            $type = $_POST['type'];
            $user_id = $_SESSION['user_id'] ?? null;
            

            // Save submitted data in session in case of errors
            $_SESSION['form_data'] = [
                'title' => $title,
                'description' => $description,
                'xp_reward' => $xp,
                'coins_reward' => $coins,
                'type' => $type,
                'error' => ''
            ];


            // If no validation errors
            if (
                empty($_SESSION['form_data']['titleErr']) &&
                empty($_SESSION['form_data']['descriptionErr'])
            ) {
                $questModel = $this->model('Quests');
                
                $inserted = $questModel->create([
                    'title' => $title,
                    'description' => $description,
                    'xp_reward' => $xp,
                    'coins_reward' => $coins,
                    'type' => $type,
                    'created_by' => $user_id,
                    
                ]);

                if ($inserted) {
                    unset($_SESSION['form_data']);
                    $_SESSION['success_message'] = 'Quest created successfully!';
                    header("Location: " . ROOT . "/admin/quests");
                    exit;
                } else {
                    $_SESSION['form_data']['error'] = 'Failed to create quest. Try again.';
                }
            }

            // Redirect back if validation failed
            header("Location: " . ROOT . "/admin/quests/create");
            exit;
        }

        // If session form data exists, populate $data
        if (isset($_SESSION['form_data'])) {
            $data = $_SESSION['form_data'];
            unset($_SESSION['form_data']);
        }

        // Load view
        $this->view('/admin/dashboard', $data);
    }

    
    
    
}