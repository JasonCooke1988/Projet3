<?php

use App\Router;
use Tracy\Debugger;

require_once '../vendor/autoload.php';



if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$router = new Router();

Debugger::enable();

$router->run();
