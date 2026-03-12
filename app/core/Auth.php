<?php

class Auth
{
    public static function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public static function requireLogin()
    {
        if (!self::isLoggedIn()) {
            header("Location: " . ROOT . "/login");
            exit;
        }

    }

    public static function user()
    {
        return $_SESSION['user'] ?? null;
    }
}