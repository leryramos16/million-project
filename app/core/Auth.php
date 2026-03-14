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


    public static function isAdmin()
    {
        return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
    }

    public static function requireAdmin()
    {
        if (!self::isAdmin()) {
            header("Location: " . ROOT . "/mainpage");
            exit;
        }
    }
}