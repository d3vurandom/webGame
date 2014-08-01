<?php
    require_once('./include/authentication.class.php');
    require_once('./include/user.class.php');
    require_once('./include/chat.class.php');
    require_once('./include/config.php');

    if(!authentication::isAuthenticated()){
        echo "You are not authenticated!";
        echo '<script>setTimeout(function(){
                      window.location = "./index.php";
                    }, 1000);</script>';
        exit(0);
    }

    if(isset($_POST['channelName']) && $_POST['channelName'] !=""){
        $channelName = filter_var($_POST['channelName'],FILTER_SANITIZE_STRING);
        if($myUserID = authentication::getUserIDFromToken()){
            if($channelMembers = chat::getChannelMemberList($channelName)){
                if(count($channelMembers) > 0){
                    foreach($channelMembers as $userID){
                        $firstName = user::getFirstNameByID($userID);
                        $lastName = user::getlastNameByID($userID);
                        $username = user::getUsernameByID($userID);

                        if($userID == $myUserID){
                            echo "<div class='memberListSelf'>";
                        }else {
                            echo "<div class = 'memberList'>";
                        }
                        echo "(" . $username . ") - " . $firstName . " " . $lastName;
                        echo "</div>";
                    }
                }
            }
        }
    }


?>