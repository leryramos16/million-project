<?php 

    class LogoutController
    {

        use Controller;

        public function index()
        {
            $userModel = $this->model('User');

            // If a remember_token cookie exists, remove it and clear token in DB
            if (isset($_COOKIE['remember_token'])) {
                //idelete to ang cookie sa database
                setcookie('remember_token', '', time() - 3600, "/", "", false, true);

                //alisin ang token sa database (for security)
                if (isset($_SESSION['user_id'])) {
                    $userModel->updateRememberToken($_SESSION['user_id'], null);
                }
            }

            session_unset();       // clear all session variables
            session_destroy();     // destroy the session
            header("Location: " . ROOT . "/login");
            exit;
        }

    }

