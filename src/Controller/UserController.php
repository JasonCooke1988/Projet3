<?php
namespace App\Controller;
use App\Model\Factory\ModelFactory;
use App\Model\UserModel;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class UserController extends MainController
{
    /*
     * Error variables
     */

    public $userNameError = "Veuillez renseigner un nom d'utilisateur valide.";

    public $sameUserNameError = "Un compte utilisateur avec ce nom existe déja, veuillez essayer un autre nom.";

    public $answerError = "La réponse que vous avez donné est fausse.";

    public $passwordMatchError = 'Les mots de passe ne sont pas identiques, veuillez réessayer';

    public $newPasswordAlert = 'Votre nouveau mot de passe à étais enregistré';

    public $passwordError = "Mot de passe incorrect veuillez reesayer. ";

    public $passwordIncorrectError = "Le mot de passe saisie n'est pas correct.";

    /*
     * Alert variables
     */

    public $signUpComplete = "Félications votre compte à été créer avec succés! Vous pouvez désormais vous connecter!";



    /*
    * Variable used for stocking POST data
    */
    public $user = [];

    /*
     * Stocks the user data of POST into $user
     * hashes password if there is one
     */
    public function stockUserData()
    {
        foreach ($_POST as $key => $value)
        {
            $data[$key] = $value;
        }
        if(isset($data['password'])) {
            $data['hashedPassword'] = password_hash($data["password"], PASSWORD_DEFAULT);
        }
        return $data;
    }
    /*
     * Stores $user variables in $_SESSION
     */
    public function createSessionData()
    {
        $data =[
            'firstName' => $this->user['firstName'],
            'lastName' => $this->user['lastName'],
            'userName' => $this->user['userName']
        ];
        return $data;
    }


    /*=======================SIGN UP=======================*/

    /*
     * Creates user data in database
     */
    public function signupMethod()
    {
        $this->user = $this->stockUserData();
        /*
         * Checks if both passwords from form are equal
         */
        if ($this->user['password'] == $this->user['cpassword']) {
            /*
             * Checks if user name doesnt already exist in database
             */
            if(!ModelFactory::getModel('user')->readData($this->user['userName'], 'userName')){
                ModelFactory::getModel('User')->createData(
                    [
                        'userName' => $this->user['userName'],
                        'password' => $this->user['hashedPassword'],
                        "firstName" => $this->user['firstName'],
                        'lastName' => $this->user['lastName'],
                        'secretQuestion' => $this->user['secretQuestion'],
                        'secretAnswer' => $this->user['secretAnswer']]);
                return $this->render('signin.twig', ['alert' => $this->signUpComplete]);
            } else {
                return $this->render('signup.twig', ['signuperror' => $this->sameUserNameError]);
            }
        } else {
            return $this->render('signup.twig', ['signuperror' => $this->passwordError]);
        }
    }


    /*=======================SIGN IN AND OUT=======================*/
    /*
     * Check if user name and password from POST has a match in database
     * if so render home and stock appropriate variables in _SESSION.
     * If entered values dont have a match, display error.
     */

    /*
     * Renders the Sign in page
     */
    public function signInMethod()
    {
        return $this->render('signin.twig');
    }

    /*
   *Checks password verification, store user name, first name & last name in $_SESSION
   */
    public function signInActionMethod()
    {
        $this->user = $this->stockUserData();
        if(ModelFactory::getModel('user')->readData($this->user['userName'], 'userName')) {
            if(ModelFactory::getModel('User')->checkPassword($this->user['userName'], $this->user['password'])) {
                if($this->user['password'] == $this->user['cpassword']){
                    $this->user = ModelFactory::getModel('User')->getSessionData($this->user['userName']);
                    $_SESSION = $this->createSessionData();
                    $this->redirect('home', ['userName' => $_SESSION['userName'], 'firstName' => $_SESSION['firstName'], 'lastName' => $_SESSION['lastName']]);
                } else {
                    return $this->render('signin.twig',['error' => $this->passwordMatchError]);
                }
            } else {
                return $this->render('signin.twig', ['error' => $this->passwordIncorrectError]);
            }
        } else {
            return $this->render('signin.twig',['error' => $this->sameUserNameError]);
        }
    }

    /*
     * Destroys Session
     */
    public function signOutActionMethod()
    {
        session_destroy();
        $this->redirect('home');
    }


    /*=======================PASSWORD RESET=======================*/
    /*
     * Gets the secret question relative to the username in the database and renders it
     * Checks if the answer given match's the secret answer in the database
     * Changes Password in the database
     */


    public function passwordResetMethod()
    {
        return $this->render('passreset.twig');
    }

    /*
    * Fetch's secret question from database relative to the user name given in POST
    */
    public function questionGetMethod()
    {
        $this->user = $this->stockUserData();
        $_SESSION['namePasswordReset'] = $this->user['userName'];
        $secretQuestion =  ModelFactory::getModel('User')->secretQuestionGet($_SESSION['namePasswordReset']);
        if($secretQuestion != false) {
            return $this->render('passreset.twig', ['userName' => $this->user['userName'], 'secretQuestion' => $secretQuestion]);
        } else {
            return $this->render('signin.twig', ['passResetError' => $this->userNameError]);
        }
    }

    /*
    * Checks if the secret answer match's the one from the form
    */
    public function checkAnswerMethod()
    {
        $this->user = $this->stockUserData();
        $secretAnswerGet = ModelFactory::getModel('User')->secretAnswerGet($_SESSION['namePasswordReset']);
        if ($secretAnswerGet == $this->user['secretAnswer']) {
            return $this->newPasswordMethod();
        } else {
            return $this->render('passreset.twig', ['error' => $this->answerError]);
        }
    }

    /*
     * Checks if both password values are equal
     * If so changePassword (hashed)
     * Clears the SESSION variable used for the password change
     */
    public function newPasswordMethod()
    {
        $this->user = $this->stockUserData();
        if($this->user['password'] == $this->user['cpassword']) {
            ModelFactory::getModel('User')->updateData($_SESSION['namePasswordReset'],['password' => $this->user['hashedPassword']], 'userName');
            unset($_SESSION['namePasswordReset']);
            return $this->render('signin.twig',['alert' => $this->newPasswordAlert]);
        } else {
            return $this->render('passreset.twig',['error' => $this->passwordMatchError]);
        }
    }

    /*
     * Checks if there is any data in POST, try signing up
     * Else return to signup form
     */
    public function defaultMethod()
    {
        if(!empty($_POST)) {
            $this->signupMethod();
        } else {
            return $this->render('signup.twig');
        }
    }
}