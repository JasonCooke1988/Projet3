<?php
namespace App\Controller;
use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
/**
 * Class HomeController
 * Manages the Homepage
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
            $partnerController = new PartnerController();
            $partnerData = $partnerController->getAllPartners();
            return $this->render('home.twig', array(
                'pageData' => $_SESSION,
                'partnersList' => $partnerData,
            ));
        } else {
            $this->redirect('User');
        }
    }
}