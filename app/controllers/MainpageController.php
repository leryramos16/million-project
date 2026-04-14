<?php

class MainPageController
{
    use Controller;

    public function index()
    {
        Auth::requireLogin();

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['user_id'])) {
            header("Location: " . ROOT . "/login");
            exit;
        }

        // ONLY LOAD VIEW
        $this->view('mainpage');
    }
}