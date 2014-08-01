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
    $currentPage = "home";
?>
    <?php require_once('./include/header.php');?>
    <script>
        $(document).ready(function() {
            scrollDivToBottom("globalChatContent")
            setInterval(function() {getChatMessageUpdates("global", lastMessage)}, 1200);
            getChannelMembers("global");
        });
    </script>
        <div id='globalChatContainer'>
            <div id='globalChat'>
                <h3> Global Chat</h3>
                <hr>
                <?php
                    //get global messages
                    $globalMessages =  chat::getChannelMessages("global");
                    $lastMessage = 0;

                    echo "<div id='globalChatContent' class='chatContainer'/>";
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
        <div id="channelMembersContainer">
            <div id="channelMembers">
            </div>
        </div>
        <footer>
            <?php include('./include/footer.php') ?>
        </footer>
    </body>
</html>