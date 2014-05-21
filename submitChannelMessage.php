<?php
    require_once('./include/authentication.class.php');
    require_once('./include/user.class.php');
    require_once('./include/chat.class.php');
    require_once('./include/config.php');

    if(isset($_POST['channel']) && isset($_POST['message'])){
        $channelName = filter_var($_POST['channel'],FILTER_SANITIZE_STRING);
        $message = filter_var($_POST['message'],FILTER_SANITIZE_STRING);
        if(chat::putChannelMessage($channelName,$message)){
            echo "true";
            return true;
        }else {
            echo "false";
            return false;
        }
    }else {
        echo "false";
        return false;
    }
?>