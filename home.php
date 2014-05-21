<?php
    require_once('./include/authentication.class.php');
    require_once('./include/user.class.php');
    require_once('./include/chat.class.php');
    require_once('./include/config.php');

    global $maximumIdleTime;

    if(!authentication::isAuthenticated()){
        echo "You are not authenticated!";
        echo '<script>setTimeout(function(){
              window.location = "./index.php";
            }, 1000);</script>';
        exit(0);
    }
    $myUserID =  authentication::getUserIDFromToken();
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Home Page</title>

        <script src="./javascript/jQuery_1.11.0.js" type="text/javascript"></script>
        <script src="./javascript/login.js" type="text/javascript"></script>
        <script src="./javascript/authentication.js" type="text/javascript"></script>
        <script src="./javascript/chat.js" type="text/javascript"></script>
        <link href="./css/normalize_3.0.css" rel="stylesheet" type="text/css">
        <link href="./css/bootstrap_3.1.1.css" rel="stylesheet" type="text/css">
        <link href="./css/style.css" rel="stylesheet" type="text/css">
        <?php echo "<script> var myUserID = " . $myUserID . "</script>"; ?>
        <meta charset="utf-8">

    </head>

    <body onLoad="authenticationTimer( <?php echo $maximumIdleTime ?> )">

        <div id='title'>
            <h2>Global Chat</h2>
        </div>

        <div id='homeBoxContainer'>
            <div id='homeBox'>
                <a href="./logout.php">Logout</a>
                <div id="countdownTimer">timer</div>
                <?php
                    $myUserID =  authentication::getUserIDFromToken();
                ?>
                <br/> <br/>
                <?php
                    //get global messages
                    $globalMessages =  chat::getChannelMessages("global");
                    $lastMessage = 0;

                    echo "<div id='globalChat' class='chatContainer'/>";
                            foreach($globalMessages as $message){
                                if($message['messageID'] > $lastMessage){
                                    $lastMessage = $message['messageID'];
                                }
                                $userID = $message['userID'];
                                $firstName = $message['firstName'];
                                $lastName = $message['lastName'];
                                $timestamp = $message['timestamp'];
                                $message = $message['message'];

                                if($myUserID == $userID){
                                    echo "<div class='bubbledRight'/>";
                                }
                                else {
                                    echo "<div class='bubbledLeft'/>";
                                }
                                echo $firstName . " " . $lastName . ": " . $message . " at " . $timestamp;
                                echo "</div>";
                            }
                    echo "</div>";
                    echo "<script> var lastMessage = " . $lastMessage . "</script>";
                ?>
                <br>
                <div id="messageInput"/>
                    <form name='chatMessage' onsubmit="return false">
                        <table id='newChatMessageTable'>
                            <tr>
                                <td id='newChatMessageTest'>
                                    <input type="text" class="form-control" name="message"
                                           autocomplete="off" placeholder  = 'Send Message'
                                           onkeyup="checkIfEnterKey_message(event, 'global')"/>
                                </td>
                                <td id='newChatMessageButton'>
                                    <button class="btn btn-primary" onclick="submitMessage('global')">Submit</button>
                                </td>
                            </tr>
                        </table>



                    </form>
                </div>
            </div>
        </div>
    </body>
</html>