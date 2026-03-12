<?php

class MainPageController
{
    use Controller;

    public function __construct()
    {
        
    }

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

        $this->view('mainpage');
    }
}
