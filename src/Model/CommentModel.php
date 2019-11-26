<?php


namespace App\Model;


class CommentModel extends MainModel
{
    /**
     * Gets a comment if there is one assigned to the current partner
     * by the current user
     */
    public function getComment(string $userName, string $partnerName)
    {
        $query = 'SELECT post FROM ' . $this->table . ' WHERE userID = \'' . $userName . '\' AND partnerID = \'' . $partnerName . '\'';
        return $this->database->getData($query);
    }
}

