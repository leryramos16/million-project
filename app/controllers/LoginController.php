<?php 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * login class
 */
class LoginController
{
    use Controller;
    public function index()
    {   

        $data = [
            'usernameOrEmail' => '',
            'usernameOrEmailErr' => '',
            'password' => '',
            'passwordErr' => '',
            'error' => ''
        ];

       if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $usernameOrEmail = trim($_POST['usernameOrEmail']);
            $password = $_POST['password'];

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
            $userModel = $this->model('User');
            $user = $userModel->findByUsernameOrEmail($usernameOrEmail);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user'] = $user;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['just_logged_in'] = true; //Welcome back appear isang beses lang pag log-in
                $_SESSION['email'] = $user['email'];
                
                

            //Para sa remember me feature
            if (!empty($_POST['remember'])) {
                $token = bin2hex(random_bytes(32));

                //isave ang token sa database
                $userModel->updateRememberToken($user['id'], $token);

                //pang set ng cookie(30 days to)
                setcookie('remember_token', $token, time() + (86400 * 30), "/", "", false, true);

            }

            if ($user['role'] === 'admin') {
                header("Location: " . ROOT . "/admin");
                exit;
            } else if ($user['role'] === 'user') {
                header("Location: " . ROOT . "/mainpage");
                exit;
            }
            } else {
                $_SESSION['form_data']['error'] = 'Invalid username/email or password.';
            }
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

