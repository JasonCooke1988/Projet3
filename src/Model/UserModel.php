<?php
namespace App\Model;
use App\Model\Factory\ModelFactory;

/**
 * Class UserModel
 * Manages User Data
 * @package App\Model
 */
class UserModel extends MainModel
{
    /*
     * Gets the data to be put in the session
     */
    public function getSessionData(string $userName)
    {
        $query = 'SELECT userName, firstName, lastName FROM ' . $this->table . ' WHERE userName=?';
        return $this->database->getData($query, [$userName]);
    }

    /*
     * Checks password from login form against hashed password in database
     */
    public function checkPassword(string $userName, string $password)
    {
        $query = 'SELECT password FROM ' . $this->table . ' WHERE userName=?';
        $data = $this->database->getData($query, [$userName]);
        $hashedPassword = array_values($data);
        return password_verify($password, $hashedPassword[0]);
    }

    /*
     * Gets secret answer relative to the user name given in the database for password reset
     */
    public function secretAnswerGet(string $userName)
    {
        $query = 'SELECT secretAnswer FROM ' . $this->table . ' WHERE userName=?';
        $data = $this->database->getData($query,[$userName]);
        $secretAnswer = array_values($data);
        return $secretAnswer[0];
    }

    /*
     * Gets secret question relative to the user name given in the database
     * if there is a match for password reset returns false if no match for user name
     */
    public function secretQuestionGet(string $userName)
    {
        $query = 'SELECT secretQuestion FROM ' . $this->table . ' WHERE userName=?';
        $data = $this->database->getData($query,[$userName]);
        $secretQuestion = array_values($data);
        return $secretQuestion[0];
    }
}