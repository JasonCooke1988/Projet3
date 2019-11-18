<?php
namespace App\Controller;
use App\Model\Factory\ModelFactory;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class SignedupController extends MainController
{


    public function defaultMethod()
    {
        /*
        * Variable containing the values posted from the form in 'signup.twig'
        */
        $userName = $_POST["user_name"];
        $cpassword = $_POST["cpassword"];
        $password = $_POST["password"];
        $firstName = $_POST["first_name"];
        $lastName = $_POST["last_name"];
        $secretQuestion = $_POST["secret_question"];
        $secretAnswer = $_POST["secret_answer"];
        $passworderror = "Les deux mots de passe doivent être identique";

        if($cpassword !== $password)
        {
            return $this->render('signup.twig',['signuperror' => $passworderror]);
        }
        else
        {
            $signup= ModelFactory::getModel('User')->createData(
                ['user_name' => $userName,'password' => $password, "first_name" => $firstName, 'last_name' => $lastName,
                    'secret_question' => $secretQuestion, 'secret_answer' =>$secretAnswer]);
            return $this->render('signedup.twig');
        }
    }
}