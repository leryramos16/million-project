<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'config.php';
require 'functions.php';
require 'Database.php';
require 'Model.php';
require 'Controller.php';
require 'Auth.php';
require 'App.php';


//Remember me auto log-in
if (!isset($_SESSION['user_id']) && isset($_COOKIE['remember_token'])) {
    require_once __DIR__ . '/../models/User.php';
    $userModel = new User();
    $user = $userModel->findByToken($_COOKIE['remember_token']);

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
    }
}

spl_autoload_register(function($classname){
    
    require $filename = "../app/models/".ucfirst($classname).".php";
});



