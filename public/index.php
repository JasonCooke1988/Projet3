<?php

use App\Router;

require_once '../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router();
$router->run();
