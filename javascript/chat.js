function submitMessage(channel) {
    var message = $('form[name="chatMessage"]').find('input[name="message"]');

    if(message.val().length > 0){
        postAjaxRequest("./submitChannelMessage.php","channel=" + channel + "&message=" + message.val(), function (response) {
            if(response == "true"){
                message.val("");
                scrollDivToBottom("globalChatContent");
            }
            else{
            }
        }) ;
    }
}

function postAjaxRequest(file, data, onSuccess, onFailure){
        return $.ajax({
        type: "POST",
        url: file,
        data: data,
        success: onSuccess,
        error: onFailure
    });
}

function checkIfEnterKey_message(event, channel){
   if(event.keyCode == 13){
       submitMessage(channel);
    }
};

function getChatMessageUpdates(channel,lastMessageReceived){

    postAjaxRequest("./getChannelMessageUpdate.php","channel=" + channel + "&lastMessageID=" + lastMessageReceived, function (response) {
        if(response != "false" && response != ""){
            var updates = $.parseJSON(response);

            for(var message in updates) {
                var messageDIV = null;

                if(updates[message]['userID'] == myUserID){
                    messageDIV += "<div class = 'bubbledRight'>";
                }else{
                    messageDIV += "<div class = 'bubbledLeft'>";
                }
                messageDIV += updates[message]['firstName'] + " " + updates[message]['lastName'] + " " + updates[message]['message'] + " at " + updates[message]['timestamp'];

                messageDIV += "</div>";
                if(updates[message]['messageID'] > lastMessage){
                    lastMessage = updates[message]['messageID'];
                }
                $(".chatContainer").append(messageDIV);
            }

            //alert(myUserID);

            scrollDivToBottom("globalChat")
        }
        else {
            console.log("no new updates since " + lastMessageReceived);
        }
    }) ;
}

function scrollDivToBottom(divName){
    var div = document.getElementById(divName);
    $(div).animate({ scrollTop: $("#globalChatContent")[0].scrollHeight}, 2000);
}


function getChannelMembers(channelName){

    postAjaxRequest("./getChannelMembersList.php","channelName=" + channelName, function (response) {
        if(response != "false" && response != ""){

                var value = "<h3>Channel Members</h3>";
                value += "<hr>";
                value += response;
            $('#channelMembers').html(value);
        }
    });
}