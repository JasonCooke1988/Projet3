<?php


namespace App\Model;


class VoteModel extends MainModel
{

    /*
     *
     */
    public function getVoteCount(string $partnerName)
    {
        $query = 'SELECT vote FROM ' . $this->table . ' WHERE partnerID =? AND vote =?';

        return $array = [
            'likeCount' => count($this->database->getAllData($query,[$partnerName,'like'])),
            'dislikeCount' => Count($this->database->getAllData($query,[$partnerName,'dislike'])),
        ];
    }

    /*
     * Gets the vote state : Like / Dislike / false (no data match)
     * from the user's vote relative to the partner name
     */
    public function getVoteState(string $userName, string $partnerName)
    {
        $query = 'SELECT vote FROM ' . $this->table . ' WHERE userID =? AND partnerID =?';
        return $this->database->getData($query, [$userName, $partnerName]);
    }

    /*
     * Update the vote state or creates an entry in the database
     * if no match's relative to user name and partner name
     */
    public function updateOrCreateVote(string $userName, string $partnerName, string $voteState)
    {
        if($this->getVoteState($userName, $partnerName) == false){
            return $this->createData([
                'userID' => $userName,
                'partnerID' => $partnerName,
                'vote' => $voteState
            ]);
        } else  {
            $query = 'UPDATE ' . $this->table . ' SET vote = \'' . $voteState . '\' WHERE userID =\'' . $userName . '\' AND partnerID =\'' . $partnerName . '\'';
            return $this->database->setData($query);
        }
    }
}