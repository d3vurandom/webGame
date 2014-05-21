$(document).ready(function() {
    scrollDivToBottom("globalChat")
    setInterval(function() {getChatMessageUpdates("global", lastMessage)}, 1200);
});

function submitMessage(channel) {
    var message = document.chatMessage.message.value;

    postAjaxRequest("./submitChannelMessage.php","channel=" + channel + "&message=" + message, function (response) {
        if(response == "true"){
            var message = $('form[name="chatMessage"]').find('input[name="message"]');
            message.val("");
            scrollDivToBottom("globalChat")
        }
        else{
        }
    }) ;

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
    $(div).animate({ scrollTop: $("#globalChat")[0].scrollHeight}, 2000);
}