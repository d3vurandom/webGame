<?php
require_once("./include/database.class.php");

class user{
    /**
     * @param $username
     * @param $passwordHashed (already hashed with sha512)
     * @return bool  returns an integer ID of the userID
     */
    public function getUserID($username,$passwordHashed){
        $username = filter_var($username,FILTER_SANITIZE_STRING);

        $sqlQuery="SELECT userID
                    FROM users
                    WHERE username=? AND password=? AND isActive=true LIMIT 1";
        $parameters = array ($username, $passwordHashed);
        $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

        /* Bind result */
        if (!($statement->bind_result($userID))) {
            echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
        }

        if($statement->num_rows == 1 && $statement->fetch()){
            database::getInstance()->closeDB($statement);

            return $userID;
        }
        database::getInstance()->closeDB($statement);
        return false;
    }
    /**
     * @param $username
     * @return bool checks is the supplied username exists in the database or not.
     */
    public function isUsernameAvailable($username){
        $username = filter_var($username,FILTER_SANITIZE_STRING);

        $sqlQuery="SELECT username
                    FROM users
                    WHERE username=? LIMIT 1";
        $parameters = array ($username);
        $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

        /* Bind result */
        if (!($statement->bind_result($username))) {
            echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
        }

        if($statement->num_rows != 0 && $statement->fetch()){
            database::getInstance()->closeDB($statement);
            return false;
        }

        database::getInstance()->closeDB($statement);
        return true;
    }

    /**
     * @param $username
     * @param $firstName
     * @param $lastName
     * @param $emailAddress
     * @param $password
     * @return bool
     * this function is used to create an account it does all of the sensitization as well.
     */
    public function createUserAccount($username,$firstName,$lastName,$emailAddress,$password){
        $username = filter_var($username,FILTER_SANITIZE_STRING);
        $firstName = filter_var($firstName,FILTER_SANITIZE_STRING);
        $lastName = filter_var($lastName,FILTER_SANITIZE_STRING);
        $emailAddress = filter_var($emailAddress,FILTER_SANITIZE_EMAIL);
        $passwordHashed = hash('sha512', $password);


        $sqlQuery="INSERT INTO users
                    (username,password,firstName,lastName,emailAddress)
                    VALUES(?,?,?,?,?)";
        $parameters = array ($username,$passwordHashed,$firstName,$lastName,$emailAddress);
        $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

        if($statement->num_rows != 0 && $statement->fetch()){
            database::getInstance()->closeDB($statement);
            return false;
        }

        database::getInstance()->closeDB($statement);
        return true;
    }

    /**
     * @param $userID
     * @return array|bool returns an array of firstname and lastname
     */
    public function getNameByID($userID){
        $userID = filter_var($userID,FILTER_SANITIZE_NUMBER_INT);

        $sqlQuery="SELECT firstName, lastName
                        FROM users
                        WHERE userID=? AND isActive=true";
        $parameters = array ($userID);
        $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

        /* Bind result */
        if (!($statement->bind_result($firstName, $lastName))) {
            echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
        }

        if($statement->num_rows == 1 && $statement->fetch()){
            database::getInstance()->closeDb($statement);
            return array($firstName, $lastName);
        }
        database::getInstance()->closeDb($statement);
        return false;
    }

    /**
     * @param $userID
     * @return bool Returns lastName string based on a userID
     */
    public function getLastNameByID($userID){
        $userID = filter_var($userID,FILTER_SANITIZE_NUMBER_INT);


        $sqlQuery="SELECT lastName
                        FROM users
                        WHERE userID=? AND isActive=true";
        $parameters = array ($userID);
        $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

        /* Bind result */
        if (!($statement->bind_result($lastName))) {
            echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
        }

        if($statement->num_rows == 1 && $statement->fetch()){
            database::getInstance()->closeDb($statement);
            return $lastName;
        }
        database::getInstance()->closeDb($statement);
        return false;
    }

    /**
     * @param $userID
     * @return bool returns firstName string based on a given userID
     */
    public function getFirstNameByID($userID){
        $userID = filter_var($userID,FILTER_SANITIZE_NUMBER_INT);

        $sqlQuery="SELECT firstName
                        FROM users
                        WHERE userID=? AND isActive=true";
        $parameters = array ($userID);
        $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

        /* Bind result */
        if (!($statement->bind_result($firstName))) {
            echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
        }

        if($statement->num_rows == 1 && $statement->fetch()){
            database::getInstance()->closeDb($statement);
            return $firstName;
        }
        database::getInstance()->closeDb($statement);
        return false;
    }
    /**
     * @param $userID
     * @return bool|mixed|string
     * this function returns the username given a valued userID
     */
    public function getUsernameByID($userID){
        $userID = filter_var($userID,FILTER_SANITIZE_NUMBER_INT);

        $sqlQuery="SELECT username
                        FROM users
                        WHERE userID=? AND isActive=true";
        $parameters = array ($userID);
        $statement = database::getInstance()->databaseQuery($sqlQuery,$parameters);

        /* Bind result */
        if (!($statement->bind_result($username))) {
            echo "Getting result set failed: (" . $statement->errno . ") " . $statement->error;
        }

        if($statement->num_rows == 1 && $statement->fetch()){
            database::getInstance()->closeDb($statement);
            return $username;
        }
        database::getInstance()->closeDb($statement);
        return false;
    }
}
?>