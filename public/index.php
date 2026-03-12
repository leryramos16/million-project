<?php

// Set timezone to Philippine time
date_default_timezone_set('Asia/Manila');

session_start();

require "../app/core/init.php";
require_once __DIR__ . '/../vendor/autoload.php';

DEBUG ? ini_set('display_errors', 1) : ini_set('display_errors', 0);

$app = new App;
$app->loadController();