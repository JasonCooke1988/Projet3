<?php

namespace App\Controller;
use App\Model\Factory\ModelFactory;
use App\Model\UserModel;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class PartnerController extends MainController
{

    public $newCommentBox = false;

    public $commentsList = [];

    public $voteState = false;

    public $voteCount =[];

    public $partnerName = "";

    public $partnerData = [];

    public $alreadyCommented = 'Vous avez déja posté un commentaire pour ce partenaire.';



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
        $this->voteState = ModelFactory::getModel('Vote')->getVoteState($partnerName, $userName);
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

    /*
     * Creates the comment in the database, checks if a comment already
     * by the current user relative to the current partner
     */
    public function createCommentMethod()
    {
        $this->partnerName = $_GET[0];
        ModelFactory::getModel('comment')->createData([
            'userID' => $_SESSION['userName'],
            'partnerID' => $this->partnerName,
            'dateAdd' => date("y/m/d"),
            'post' => $_POST['post']
        ]);
        return $this->defaultMethod();
    }

    /*
     * Gets comments relative ot the partner name
     */
    public function getAllComments(string $partnerName)
    {
        return ModelFactory::getModel('Comment')->listData($partnerName, 'partnerID');
    }

    /*
     * Get one partner data to be rendered in its individual page
     */
    public function getPartner(string $partnerName)
    {
        return ModelFactory::getModel('Partner')->readData($partnerName, 'partnerName');
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
        $this->voteState = $this->getVoteState($this->partnerName, $this->userName);
        $this->voteCount = $this->getVoteCount($this->partnerName);
        return $this->render('partner.twig', [
            'error' => $error,
            'partner' => $this->partnerData,
            'pageData' => $_SESSION,
            'commentsList' => $this->commentsList,
            'voteState' => $this->voteState,
            'newCommentBox' => $this->newCommentBox,
            'voteCount' => $this->voteCount
        ]);
    }
}