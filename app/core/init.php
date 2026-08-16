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

