<?php
    require_once('./include/database.class.php');

    class chat{
        public function getChannelMessages($channelName, $limit = 100){
            $limit = filter_var($channelName,FILTER_SANITIZE_NUMBER_INT);
            if($limit <= 0 || $limit > 15000){
                $limit = 100;
            }

            $channelName = filter_var($channelName,FILTER_SANITIZE_STRING);

            $sqlQuery="SELECT DISTINCT chatMessages.messageID, chatChannels.channelName, chatMessages.timestamp, chatMessages.message, users.firstName, users.lastName, users.userID
                FROM chatChannels
                LEFT JOIN chatMessages
                ON chatChannels.channelID = chatMessages.channelID
                LEFT JOIN chatMembers
                ON chatChannels.channelID = chatMembers.channelID
                LEFT JOIN users
                ON	chatMessages.userID = users.userID
                WHERE chatChannels.channelName = ? LIMIT ?";

            $parameters = array ($channelName, $limit);
            $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

            /* Bind result */
            if (!($statement->bind_result($messageID, $channelName,$timestamp, $message, $firstName, $lastName, $userID))) {
                echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
            }

            if($statement->num_rows >= 1){
                $channelMessages = array();

                while($statement->fetch()){
                    $chatEntity = array();
                    $chatEntity['channelName']= $channelName;
                    $chatEntity['timestamp'] = $timestamp;
                    $chatEntity['message'] = $message;
                    $chatEntity['firstName'] = $firstName;
                    $chatEntity['lastName'] = $lastName;
                    $chatEntity['userID'] = $userID;
                    $chatEntity['messageID'] = $messageID;

                    $channelMessages[$messageID] = $chatEntity;
                }
                return $channelMessages;
                database::getInstance()->closeDB($statement);
            }
            database::getInstance()->closeDB($statement);
            return false;
        }

        public function getChannelMembers($channelName){

            $channelName = filter_var($channelName,FILTER_SANITIZE_STRING);
            $sqlQuery="SELECT chatMembers.userID
                        FROM chatChannels
                        LEFT JOIN chatMembers
                        ON chatChannels.channelID = chatMembers.channelID
                        WHERE chatChannels.channelName = ?";

            $parameters = array ($channelName);
            $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

            /* Bind result */
            if (!($statement->bind_result($userID))) {
                echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
            }

            if($statement->num_rows >= 1){
                $userID_array = array();

                while($statement->fetch()){
                    array_push($userID_array,$userID);
                }
                database::getInstance()->closeDB($statement);
                return $userID_array;
            }
            database::getInstance()->closeDB($statement);
            return false;
        }

        public function joinChannel($channelName){
            //filter channel Name
            $channelName = filter_var($channelName,FILTER_SANITIZE_STRING);

            //get UserID
            $myUserID = authentication::getUserIDFromToken();

            //see if the channel exists, if not create it
            if(!$channelID = chat::getChannelID($channelName)){
                //need to create the channel
                $sqlQuery="INSERT INTO chatChannels
                        (channelName)
                        VALUES(?)";
                $parameters = array ($channelName);
                $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

                if($statement->num_rows != 0 && $statement->fetch()){
                    database::getInstance()->closeDB($statement);
                    return false;
                }
                database::getInstance()->closeDB($statement);
            }
            else {
                //see if I am a member of the channel, if not then add me

                //get the userIDs that are in the channel
                $usersInChannel = chat::getChannelMembers($channelName);

                if(!in_array($myUserID,$usersInChannel)){
                    $sqlQuery="INSERT INTO chatMembers
                        (channelID,userID)
                        VALUES(?,?)";
                    $parameters = array ($channelID,$myUserID);
                    $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

                    if($statement->num_rows != 0 && $statement->fetch()){
                        database::getInstance()->closeDB($statement);
                        return false;
                    }
                    database::getInstance()->closeDB($statement);
                    return true;
                }
                else {
                    return true;
                }
            }
        }

        public function putChannelMessage($channelName,$message){
            $channelName = filter_var($channelName,FILTER_SANITIZE_STRING);
            $message = filter_var($message,FILTER_SANITIZE_STRING);

            $channelID = chat::getChannelID($channelName);
            $myUserID = authentication::getUserIDFromToken();
            echo $channelName . " " . $message . " " . $channelID . " " . $myUserID;
            if(is_int((int)$myUserID) && in_array($myUserID, chat::getChannelMembers($channelName))){
                $sqlQuery="INSERT INTO chatMessages
                        (channelID,userID,message)
                        VALUES(?,?,?)";
                $parameters = array ($channelID,$myUserID,$message);
                $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

                if($statement->num_rows != 0 && $statement->fetch()){
                    database::getInstance()->closeDB($statement);
                    return false;
                }
                database::getInstance()->closeDB($statement);
                return true;
            }
            else {
                return false;
            }
        }

        public function getChannelMessagesUpdate($channelName,$lastMessage){
            $channelName = filter_var($channelName,FILTER_SANITIZE_STRING);
            $lastMessage = filter_var($lastMessage,FILTER_SANITIZE_NUMBER_INT);
            $myUserID = authentication::getUserIDFromToken();
            if(chat::isUserInChannel($channelName,$myUserID)){

                $channelID = chat::getChannelID($channelName);

                $sqlQuery="SELECT DISTINCT chatMessages.messageID, chatChannels.channelName, chatMessages.timestamp, chatMessages.message, users.firstName, users.lastName, users.userID
                FROM chatChannels
                LEFT JOIN chatMessages
                ON chatChannels.channelID = chatMessages.channelID
                LEFT JOIN chatMembers
                ON chatChannels.channelID = chatMembers.channelID
                LEFT JOIN users
                ON	chatMessages.userID = users.userID
                WHERE chatMessages.channelID = ? AND chatMessages.messageID > ?";

                $parameters = array ($channelID,$lastMessage);
                $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

                /* Bind result */
                if (!($statement->bind_result($messageID, $channelName,$timestamp, $message, $firstName, $lastName, $userID))) {
                    echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
                }

                if($statement->num_rows >= 1){
                    $channelMessages = array();

                    while($statement->fetch()){
                        $chatEntity = array();
                        $chatEntity['channelName']= $channelName;
                        $chatEntity['timestamp'] = $timestamp;
                        $chatEntity['message'] = $message;
                        $chatEntity['firstName'] = $firstName;
                        $chatEntity['lastName'] = $lastName;
                        $chatEntity['userID'] = $userID;
                        $chatEntity['messageID'] = $messageID;

                        $channelMessages[$messageID] = $chatEntity;
                    }
                    return $channelMessages;
                    database::getInstance()->closeDB($statement);
                }
                database::getInstance()->closeDB($statement);
                return false;

            }
            else {
                return false;
            }
        }

        public function getChannelID($channelName){
            $channelName = filter_var($channelName,FILTER_SANITIZE_STRING);

            $sqlQuery="SELECT channelID FROM chatChannels
                        WHERE channelName = ?";

            $parameters = array ($channelName);
            $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

            /* Bind result */
            if (!($statement->bind_result($channelID))) {
                echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
            }

            if($statement->num_rows == 1){
                $channelIDNumber = null;

                if($statement->fetch()){
                    $channelIDNumber = $channelID;
                }
                database::getInstance()->closeDB($statement);
                return $channelIDNumber;
            }
            database::getInstance()->closeDB($statement);
            return false;
        }
        public function isUserInChannel($channelName,$userID){
            $channelName = filter_var($channelName,FILTER_SANITIZE_STRING);
            $userID = filter_var($userID,FILTER_SANITIZE_NUMBER_INT);

            if($channelMembers = chat::getChannelMembers($channelName)){
                if(in_array($userID,$channelMembers)){
                    return true;
                }
                else {
                    echo "here";
                    return false;
                }
            }else {
                echo "here1";
                return false;
            }
        }
    }