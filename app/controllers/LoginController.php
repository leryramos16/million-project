<?php

use Leryr\Mymillionpesoproject\Repositories\UserRepository;

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Admin/Game Master web login. Regular players authenticate through the Flutter app's API.
 */
class LoginController
{
    use Controller;

    public function index()
    {
        $data = [
            'usernameOrEmail' => '',
            'usernameOrEmailErr' => '',
            'passwordErr' => '',
            'error' => ''
        ];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usernameOrEmail = trim($_POST['usernameOrEmail'] ?? '');
            $password = $_POST['password'] ?? '';

            $_SESSION['form_data'] = [
                'usernameOrEmail' => $usernameOrEmail,
                'usernameOrEmailErr' => '',
                'passwordErr' => '',
                'error' => ''
            ];

            if (empty($usernameOrEmail)) {
                $_SESSION['form_data']['usernameOrEmailErr'] = '*Username is required';
            }
            if (empty($password)) {
                $_SESSION['form_data']['passwordErr'] = '*Password is required';
            }

            if (
                empty($_SESSION['form_data']['usernameOrEmailErr']) &&
                empty($_SESSION['form_data']['passwordErr'])
            ) {
                $userRepository = new UserRepository((new Database())->connect());
                $user = $userRepository->findByUsernameOrEmail($usernameOrEmail);

                if ($user && password_verify($password, $user['password']) && $user['role'] === 'admin') {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['email'] = $user['email'];

                    unset($_SESSION['form_data']);
                    header("Location: " . ROOT . "/admin");
                    exit;
                }

                $_SESSION['form_data']['error'] = 'Invalid credentials, or this account is not a Game Master.';
            }

            header("Location: " . ROOT . "/login");
            exit;
        }

        if (isset($_SESSION['form_data'])) {
            $data = $_SESSION['form_data'];
            unset($_SESSION['form_data']);
        }

        $this->view('login', $data);
    }
}
