<?php

class game{
    /**
     * @param $myUserID
     * @return bool
     * I never finished this function I was having many logic issues. my head hurts now.
     */
    function getMembersNotInGameWithID($myUserID){
        $myUserID = filter_var($myUserID,FILTER_SANITIZE_NUMBER_INT);

        $sqlQuery="SELECT challengeID, challenger,userIDChallenged
                        FROM gameChallenge
                        WHERE challenger !=? AND status=NULL";
        $parameters = array ($myUserID);
        $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

        /* Bind result */
        if (!($statement->bind_result())) {
            echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
        }

        if($statement->num_rows == 1 && $statement->fetch()){
            database::getInstance()->closeDb($statement);
            return $userIDList;
        }
        database::getInstance()->closeDb($statement);
        return false;
    }

    function getMembersInGameWithID($myUserID){

    }
    function challengeMember($myUserID,$userID){

    }
    function acceptChallengeWithMember($gameID,$myUserID){

    }
    function declineChallengeWithMember($gameID,$myUserID){

    }
    function createNewGame($myUserID,$theirUserID){

    }
    function restartGameRequest($gameID,$myUserID){

    }
    function startGame ($gameID){

    }
}
