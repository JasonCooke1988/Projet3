<?php
/* Get the necessary elements by using namespaces (uncomment Tracy to get the Debugger) */
use App\Router;
use Tracy\Debugger;
/* Required call to load the classes with the Composer Autoload */
require_once '../vendor/autoload.php';
/*
*Checks if session exists, if not starts session
* Assigns a logged in boolean variable to be used for login authentication
*/
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
/* Create the router */
$router = new Router();
/* Test Zone (uncomment Debugger to get Tracy & var_dump to get router object) */
Debugger::enable();
//var_dump($router);
/* Run application through the router */
$router->run();