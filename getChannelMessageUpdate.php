<?php
    require_once('./include/authentication.class.php');
    require_once('./include/user.class.php');
    require_once('./include/chat.class.php');
    require_once('./include/config.php');

    if(isset($_POST['channel']) && isset($_POST['lastMessageID'])){
        $channelName = filter_var($_POST['channel'],FILTER_SANITIZE_STRING);
        $lastMessageID = filter_var($_POST['lastMessageID'],FILTER_SANITIZE_NUMBER_INT);
        if($channelUpdates = chat::getChannelMessagesUpdate($channelName,$lastMessageID)){
           echo json_encode($channelUpdates);
        }else {
            echo "false";
            return false;
        }
    }else {
        echo "false";
        return false;
    }
?>