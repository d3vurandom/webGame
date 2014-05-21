<?php
    require_once('./include/config.php');
    require_once('./include/authentication.class.php');
    require_once('./include/user.class.php');

    global $cookieName;

    // A username and a password were entered, we can assume they would like to login and receive a yummy cookie
    if(isset($_POST['username']) && $_POST['username'] != "" && isset($_POST['password']) && $_POST['password'] != ""){

        //Filter username
        $username = filter_var($_POST['username'],FILTER_SANITIZE_STRING);

        //Hash password  I really don't care what is contains because I am just gonna hash it
        $passwordHashed = hash('sha512', $_POST['password']);

        if($userID = user::getUserID($username,$passwordHashed)){
            if($token = authentication::createAuthenticationToken($userID)){
                if(authentication::createAuthenticationCookie($cookieName, $token)){

                    // This is what javascript is looking for
                    echo "true";

                }
            }
        }
    }else if(isset($_POST['username']) && $_POST['username'] != "" && !isset($_POST['password'])){
        $username = filter_var($_POST['username'],FILTER_SANITIZE_STRING);
        if(user::isUsernameAvailable($username)){
            echo "true";
        }
    }
    else{

    }
?>