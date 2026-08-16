<?php

class LogoutController
{
    use Controller;

    public function index()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        session_unset();
        session_destroy();
        header("Location: " . ROOT . "/login");
        exit;
    }
}
