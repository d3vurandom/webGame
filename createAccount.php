<?php
    require_once('./include/user.class.php');
    require_once('./include/chat.class.php');
    require_once('./include/authentication.class.php');

    if(isset($_POST['username'])
        && $_POST['username'] != ""
        && isset($_POST['firstName'])
        && $_POST['firstName'] != ""
        && isset($_POST['lastName'])
        && $_POST['lastName'] != ""
        && isset($_POST['emailAddress'])
        && $_POST['emailAddress'] != ""
        && isset($_POST['emailAddressVerify'])
        && $_POST['emailAddressVerify'] != ""
        && isset($_POST['password'])
        && $_POST['password'] != ""
        && isset($_POST['passwordVerify'])
        && $_POST['passwordVerify'] != "" ){


        $username = $_POST['username'];
        $firstName = $_POST['firstName'];
        $lastName = $_POST['lastName'];
        $emailAddress = $_POST['emailAddress'];
        $emailAddressVerify = $_POST['emailAddressVerify'];
        $password = $_POST['password'];
        $passwordVerify = $_POST['passwordVerify'];

        if($emailAddress != $emailAddressVerify && $password != $passwordVerify){
            echo "false";
            return false;
            exit();
        }
        else {
            if(user::createUserAccount($username,$firstName,$lastName,$emailAddress,$password)){
                if(chat::joinChannel($channelName)){
                    echo "true";
                    return true;
                    exit();
                }

            }else{
                echo "false";
                return false;
                exit();
            }
        }
    }
?>