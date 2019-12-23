<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;
use App\Model\UserModel;

class PartnerController extends MainController
{

    public $userName = "";

    public $newCommentBox = false;

    public $commentCount = 0;

    public $commentsList = [];

    public $voteState = false;

    public $voteCount =[];

    public $partnerName = "";

    public $partnerData = [];

    /*
     * Error variables
     */
    public $alreadyCommented = 'Vous avez déja posté un commentaire pour ce partenaire.';


    /*=======================VOTES=======================*/


    /*
     * Gets the number of Likes and Dislikes for a partner
     */
    public function getVoteCount(string $partnerName)
    {
     return $voteCount = ModelFactory::getModel('vote')->getVoteCount($partnerName);
    }

    /*
     * Determines which like / dislike buttons to display
     */
    public function getVoteState(string $userName, string $partnerName)
    {
        $this->voteState = ModelFactory::getModel('vote')->getVoteState($partnerName, $userName);
    }

    /*
     * Changes the voteState in the database to 'like'
     */
    public function voteLikeMethod()
    {
        $this->partnerName = $_GET[0];
        $this->voteState = 'like';
        ModelFactory::getModel('vote')->updateOrCreateVote($_SESSION['userName'],$this->partnerName,'like');
        return $this->defaultMethod();
    }

    /*
     * Changes the voteState in the database to 'dislike
     */
    public function voteDislikeMethod()
    {
        $this->partnerName = $_GET[0];
        $this->voteState = 'dislike';
        ModelFactory::getModel('vote')->updateOrCreateVote($_SESSION['userName'],$this->partnerName,'dislike');
        return $this->defaultMethod();
    }

    /*=======================COMMENTS=======================*/

    /*
     * Creates the comment in the database, checks if a comment already exists
     * by the current user relative to the current partner
     */
    public function createCommentMethod()
    {
        $this->partnerName = $_GET[0];
        $this->userName = $_SESSION['userName'];
        if(ModelFactory::getModel('comment')->getComment($this->userName, $this->partnerName) == false) {
            $this->partnerName = $_GET[0];
            ModelFactory::getModel('comment')->createData([
                'userID' => $this->userName,
                'partnerID' => $this->partnerName,
                'dateAdd' => date("y/m/d"),
                'post' => $_POST['post']
            ]);
            return $this->defaultMethod();
        } else {
            return $this->defaultMethod($this->alreadyCommented);
        }

    }

    /*
     * Gets comments relative ot the partner name
     */
    public function getAllComments(string $partnerName)
    {
        return ModelFactory::getModel('comment')->listData($partnerName, 'partnerID');
    }

    /*
     * Gets the number of comments relative to the current partner
     * Leaves comment count variable at 0 if false
     */
    public function getCommentCount(string $partnerName)
    {
        if($this->getAllComments($partnerName)!=false){
            return count($this->getAllComments($partnerName));
        } else {
            return 0;
        }
    }

    /*=======================PARTNERS=======================*/

    /*
     * Get one partner data to be rendered in its individual page
     */
    public function getPartner(string $partnerName)
    {
        return ModelFactory::getModel('partner')->readData($partnerName, 'partnerName');
    }

    /*
     * Get's all partners data to be listed.
     * Takes partner description from each partner and creates a shorter description
     */
    public function getAllPartners()
    {
        $data = ModelFactory::getModel('Partner')->listData();
        foreach ($data as $key => $value) {
            foreach ($value as $k => $v) {
                if ($k == 'partnerDescription') {
                    $data[$key]['partnerShortDescr'] = substr($value[$k], 0, 70) . '...';
                }
            }
        }
        return $data;
    }

    /*
     * Renders the partner page with a comment box
     */
    public function withCommentBoxMethod()
    {
        $this->userName = $_SESSION['userName'];
        $this->partnerName = $_GET[0];
        if(ModelFactory::getModel('comment')->getComment($this->userName, $this->partnerName) == false) {
            $this->newCommentBox = true;
            return $this->defaultMethod();
        } else {
            return $this->defaultMethod($this->alreadyCommented);
        }
    }

    /*
     * Renders the partner page without a comment box
     * Accepts a paramater for error messages to be rendered with twig
     */
    public function defaultMethod($error = null)
    {
        $this->userName = $_SESSION['userName'];
        $this->partnerName = $_GET[0];
        $this->partnerData = $this->getPartner($this->partnerName);
        $this->commentsList = $this->getAllComments($this->partnerName);
        $this->commentCount = $this->getCommentCount($this->partnerName);
        $this->voteState = $this->getVoteState($this->partnerName, $this->userName);
        $this->voteCount = $this->getVoteCount($this->partnerName);
        return $this->render('partner.twig', [
            'error' => $error,
            'partner' => $this->partnerData,
            'pageData' => $_SESSION,
            'voteState' => $this->voteState,
            'voteCount' => $this->voteCount,
            'newCommentBox' => $this->newCommentBox,
            'commentCount' => $this->commentCount,
            'commentsList' => $this->commentsList
        ]);
    }
}