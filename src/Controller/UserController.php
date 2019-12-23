<?php
namespace App\Controller;
use App\Model\Factory\ModelFactory;

class UserController extends MainController
{
    /*
    * Variable used for stocking POST data
    */
    public $userName = "";

    public $user = [];

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
     * used for storeing $user variables in $_SESSION
     */
    public function createSessionData(array $user)
    {
        $data =[
            'firstName' => $user['firstName'],
            'lastName' => $user['lastName'],
            'userName' => $user['userName']
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
                ModelFactory::getModel('user')->createData(
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
   *Checks password verification, store user name, first name & last name in SESSION
   */
    public function signInActionMethod()
    {
        $this->user = $this->stockUserData();

        if(ModelFactory::getModel('user')->readData($this->user['userName'], 'userName') != false) {
            if(ModelFactory::getModel('user')->checkPassword($this->user['userName'], $this->user['password'])) {
                $sessionData = ModelFactory::getModel('User')->getSessionData($this->user['userName']);
                $_SESSION = $this->createSessionData($sessionData);
                $this->redirect('home', ['pageData' => $_SESSION]);
            } else {
                $secretQuestion =  ModelFactory::getModel('User')->secretQuestionGet($this->userName);
                return $this->render('signin.twig', ['error' => $this->passwordIncorrectError, 'secretQuestion' => $secretQuestion]);
            }
        } else {
            return $this->render('signin.twig',['error' => $this->userNameError]);
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
        $_SESSION['tempUserName'] = $this->user['userName'];
        $_SESSION['tempSecretQuestion'] =  ModelFactory::getModel('user')->secretQuestionGet($_SESSION['tempUserName']);
        if($_SESSION['tempSecretQuestion'] != null) {
            return $this->render('passreset.twig', ['userName' => $_SESSION['tempUserName'], 'secretQuestion' => $_SESSION['tempSecretQuestion']]);
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
        $secretAnswerGet = ModelFactory::getModel('user')->secretAnswerGet($_SESSION['tempUserName']);
        if ($secretAnswerGet == $this->user['secretAnswer']) {
            return $this->newPasswordMethod();
        } else {
            return $this->render('passreset.twig', ['error' => $this->answerError, 'secretQuestion' =>$_SESSION['tempSecretQuestion']]);
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
            ModelFactory::getModel('user')->updateData($_SESSION['tempUserName'],['password' => $this->user['hashedPassword']], 'userName');
            unset($_SESSION['tempUserName']);
            return $this->render('signin.twig',['alert' => $this->newPasswordAlert]);
        } else {
            return $this->render('passreset.twig',['error' => $this->passwordMatchError, 'secretQuestion' => $_SESSION['tempSecretQuestion']]);
        }
    }


    /*=======================User Account Page=======================*/


    /*
     *Function that manages the different account modification given by post
     * updates the SESSION data with the changed data
     */
    public function userDataChangeMethod()
    {
        $userData = $this->stockUserData();
        $this->userName = $_SESSION['userName'];
        switch($userData) {
            case isset($userData['firstName']):
                $updateAlert = "Votre prénom à été mis à jour.";
                ModelFactory::getModel('user')->updateData($this->userName,['firstName' => $userData['firstName']],'userName');
                $sessionData = ModelFactory::getModel('User')->getSessionData($this->userName);
                $_SESSION = $this->createSessionData($sessionData);
                return $this->render('user.twig',['pageData' => $_SESSION, 'firstNameAlert' => $updateAlert]);
                break;
            case isset($userData['lastName']):
                $updateAlert = "Votre nom de famille à été mis à jour.";
                ModelFactory::getModel('user')->updateData($this->userName,['lastName' => $userData['lastName']],'userName');
                $sessionData = ModelFactory::getModel('User')->getSessionData($this->userName);
                $_SESSION = $this->createSessionData($sessionData);
                return $this->render('user.twig',['pageData' => $_SESSION, 'lastNameAlert' => $updateAlert]);
                break;
            case isset($userData['secretAnswer'], $userData['secretQuestion']):
                $updateAlert = "Votre question et réponse secrète ont été mis à jour.";
                ModelFactory::getModel('user')->updateData($this->userName,['secretQuestion' => $userData['secretQuestion'], 'secretAnswer' => $userData['secretAnswer']],'userName');
                $sessionData = ModelFactory::getModel('User')->getSessionData($this->userName);
                $_SESSION = $this->createSessionData($sessionData);
                return $this->render('user.twig',['pageData' => $_SESSION, 'QAndAAlert' => $updateAlert]);
                break;
            case isset($userData['password']):
                if($userData['password'] == $userData['cpassword']) {
                    $updateAlert = "Votre mot de passe à étais mis à jour.";
                    ModelFactory::getModel('user')->updateData($this->userName,['password' => $userData['hashedPassword']],'userName');
                    $sessionData = ModelFactory::getModel('User')->getSessionData($this->userName);
                    $_SESSION = $this->createSessionData($sessionData);
                    return $this->render('user.twig',['pageData' => $_SESSION, 'passwordAlert' => $updateAlert]);
                } else {
                    $updateAlert = "Les mot de passe saisie ne sont pas identiques. Veuillez réessayer.";
                    return $this->render('user.twig',['pageData' => $_SESSION, 'passwordAlert' => $updateAlert]);
                }
                break;

        }
    }

    public function userAccountPageMethod()
    {
        return $this->render('user.twig',['pageData' => $_SESSION]);
    }

    /*=======================Default Method=======================*/

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