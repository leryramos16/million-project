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

            // Upload directory
            $uploadDir = "public/uploads/payments/";
            
            // Create folder if it doesn't exist
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $paymentProofName = null;

            // Handle file upload
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];

            if (isset($_FILES['payment_proof']) && $_FILES['payment_proof']['error'] === 0) {

                if (in_array($_FILES['payment_proof']['type'], $allowedTypes)) {

                    $fileName = time() . "_" . basename($_FILES['payment_proof']['name']);

                    $targetPath = $uploadDir . $fileName;

                    move_uploaded_file($_FILES['payment_proof']['tmp_name'], $targetPath);

                    $paymentProofName = $fileName;
                }
            }

            $data = [
                'title' => $_POST['title'],
                'description' => $_POST['description'],
                'payment_proof' => $paymentProofName,
                'status' => 'pending'
            ];

            $questModel->create($data);
            $_SESSION['success'] = "Quest submitted successfully! Waiting for admin approval.";
            header("Location: " . ROOT . "/addquest");
            exit;
        }

        $this->view('addquest');
    }
}