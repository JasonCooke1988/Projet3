<?php
namespace App\Controller;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
/**
 * Class HomeController
 * Manages the Homepage
 * @package App\Controller
 */
class HomeController extends MainController
{


    public function defaultMethod()
    {
        /*
         * Arrival on home page : check if SESSION user name is set if it is render home page
         * if not redirects to registration form
         */
        if (!empty($_SESSION['userName'])) {
            return $this->render('home.twig', ['user_name' => $_SESSION['userName'], 'first_name' => $_SESSION['firstName'], 'last_name' => $_SESSION['lastName']]);

        } else {
            $this->redirect('User');
        }
    }
}