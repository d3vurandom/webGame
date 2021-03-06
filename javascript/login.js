function submitLogin() {
    var username = document.loginForm.username.value;
    var password = document.loginForm.password.value;
    var messageLocation = document.getElementById("loginErrorMessage");

    postAjaxRequest("./login.php","username=" + username + "&password=" + password, function (response) {
        if(response == "true"){

            messageLocation.innerHTML = "";
            $('#loginErrorMessage').removeClass();
            $('#loginButton').removeClass();
            $('#loginButton').addClass('btn btn-success');
            $('form[name="loginForm"]').parent().addClass('has-success');
            $('#loginButton').attr('value', 'Login Successful');
            window.location.href = "./home.php";

        }
        else{
            messageLocation.innerHTML = "<center>Username or password is incorrect</center>";
            $('#loginErrorMessage').removeClass();
            $('#loginErrorMessage').addClass('loginFailure');
            $('form[name="loginForm"]').parent().addClass('has-error');
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

function checkIfEnterKeyLogin(event){
    if(event.keyCode == 13){
        submitLogin();
    }
};

function checkIfEnterKey_AccountRegistration(event){
    if(event.keyCode == 13){
        createAccount();
    }
}
function createAccount_isInputFieldBlank(inputField){
    if(inputField.val() == ""){
        inputField.prev().html("You can not leave this field blank");
        inputField.parent().removeClass();
        inputField.parent().addClass('form-group has-feedback has-warning');
        inputField.next().removeClass();
        inputField.next().addClass('glyphicon form-control-feedback glyphicon-warning-sign');
        return true;
    }
}

function createAccount(){
    var inputBlank = false;

    var username = $('form[name="accountRegistrationFrom"]').find('input[name="username"]');
    var firstName = $('form[name="accountRegistrationFrom"]').find('input[name="firstName"]');
    var lastName = $('form[name="accountRegistrationFrom"]').find('input[name="lastName"]');
    var emailAddress = $('form[name="accountRegistrationFrom"]').find('input[name="email"]');
    var emailAddressVerify = $('form[name="accountRegistrationFrom"]').find('input[name="emailVerify"]');
    var password = $('form[name="accountRegistrationFrom"]').find('input[name="password"]');
    var passwordVerify = $('form[name="accountRegistrationFrom"]').find('input[name="passwordVerify"]');
    if(createAccount_isInputFieldBlank(username)){inputBlank = true}
    if(createAccount_isInputFieldBlank(firstName)){inputBlank = true}
    if(createAccount_isInputFieldBlank(lastName)){inputBlank = true}
    if(createAccount_isInputFieldBlank(emailAddress)){inputBlank = true}
    if(createAccount_isInputFieldBlank(emailAddressVerify)){inputBlank = true}
    if(createAccount_isInputFieldBlank(password)){inputBlank = true}
    if(createAccount_isInputFieldBlank(passwordVerify)){inputBlank = true}

    if(!inputBlank){
        if((registrationCheckUsername()).then(function(response){
            if(!response){
                //alert("username Failure");
            }else {
                if(!registrationCheckFirstName()){
                    //alert("first Name Failure");
                }else if(!registrationCheckLastName()){
                    //alert("last Name Failure");
                }else if(!registrationCheckEmail("email")){
                    //alert("email 1 Failure");
                }else if(!registrationCheckEmailVerify("email","emailVerify")){
                    //alert("email 2 or 1 Failure");
                }else if(!registrationCheckPassword("password")){
                    //alert("password 1 Failure");
                }else if(!registrationCheckPasswordVerify("password","passwordVerify")){
                    //alert("password 2 or 1 Failure");
                }
                else {
                    postAjaxRequest("./createAccount.php",
                        "username=" + username.val()
                            +"&firstName=" + firstName.val()
                            +"&lastName=" + lastName.val()
                            +"&emailAddress=" + emailAddress.val()
                            +"&emailAddressVerify=" + emailAddressVerify.val()
                            +"&password=" + password.val()
                            +"&passwordVerify=" + passwordVerify.val()
                        , function (response) {

                            if(response == "true"){
                                $('#registrationModal').modal('toggle');
                            }
                            else{
                                alert("there was an error creating an account");
                            }
                        }) ;
                }
            }
        }));
    }
}

function registrationCheckUsername(){
    var username = $('form[name="accountRegistrationFrom"]').find('input[name="username"]');

    //if username == ""
    if(username.val()== ""){
        username.prev().html("&nbsp;");
        username.parent().removeClass();
        username.next().removeClass();
    }
    //if username < 3 && username > 25
    else if(username.val().length < 3 || username.val().length > 25 ){
        if(username.val().length <3){
            username.prev().html("Username must be longer than 3 characters");
        }
        else {
            username.prev().html("Username must be shorter than 25 characters");
        }
        username.parent().removeClass();
        username.next().removeClass();
        username.parent().addClass('form-group has-feedback has-error');
        username.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }

    //if username contains a special character
    else if(!isAlphanumeric(username.val())){
        username.prev().html("Username must not contain special characters");
        username.parent().removeClass();
        username.next().removeClass();
        username.parent().addClass('form-group has-feedback has-error');
        username.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }

    // if username already exists
    else{

        return postAjaxRequest("./login.php","username=" + username.val(), function (response) {
                if(response){
                    username.prev().html("That username is available");
                    username.parent().removeClass();
                    username.next().removeClass();
                    username.parent().addClass('form-group has-feedback has-success');
                    username.next().addClass('glyphicon form-control-feedback glyphicon-ok');
                    return true;
                }else {
                    username.prev().html("Sorry, username is taken");
                    username.parent().removeClass();
                    username.parent().addClass('form-group has-feedback has-warning');
                    username.next().removeClass();
                    username.next().addClass('glyphicon form-control-feedback glyphicon-warning-sign');
                }

        },function(response){
            //there was a problem with the aJax call
            return false;
        });
    }
}

function registrationCheckFirstName(){
    var firstName = $('form[name="accountRegistrationFrom"]').find('input[name="firstName"]');

    //if firstName == ""
    if(firstName.val()== ""){
        firstName.prev().html("&nbsp;");
        firstName.parent().removeClass();
        firstName.next().removeClass();
    }
    //if firstName < 1 && firstName > 25
    else if(firstName.val().length < 1 || firstName.val().length > 25 ){
        if(firstName.val().length < 1){
            firstName.prev().html("Your first name must be longer than 1 character");
        }
        else {
            firstName.prev().html("Your first name must be shorter than 25 characters");
        }
        firstName.parent().removeClass();
        firstName.next().removeClass();
        firstName.parent().addClass('form-group has-feedback has-error');
        firstName.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }
    //if firstName contains a special character
    else if(!isAlphanumeric(firstName.val())){
        firstName.prev().html("Your first name can't contain special characters");
        firstName.parent().removeClass();
        firstName.next().removeClass();
        firstName.parent().addClass('form-group has-feedback has-error');
        firstName.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }
    else {
        firstName.prev().html("&nbsp;");
        firstName.parent().removeClass();
        firstName.next().removeClass();
        firstName.parent().addClass('form-group has-feedback has-success');
        firstName.next().addClass('glyphicon form-control-feedback glyphicon-ok');
        return true;
    }
}

function registrationCheckLastName(){
    var lastName = $('form[name="accountRegistrationFrom"]').find('input[name="lastName"]');

    //if lastName == ""
    if(lastName.val()== ""){
        lastName.prev().html("&nbsp;");
        lastName.parent().removeClass();
        lastName.next().removeClass();
    }
    //if lastName < 1 && lastName > 25
    else if(lastName.val().length < 1 || lastName.val().length > 25 ){
        if(lastName.val().length < 1){
            lastName.prev().html("Your last name must be longer than 1 characters");
        }
        else {
            lastName.prev().html("Your last name must be shorter than 25 characters");
        }
        lastName.parent().removeClass();
        lastName.next().removeClass();
        lastName.parent().addClass('form-group has-feedback has-error');
        lastName.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }
    //if lastName contains a special character
    else if(!isAlphanumeric(lastName.val())){
        lastName.prev().html("Your last name can't contain special characters");
        lastName.parent().removeClass();
        lastName.next().removeClass();
        lastName.parent().addClass('form-group has-feedback has-error');
        lastName.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }
    else {
        lastName.prev().html("&nbsp;");
        lastName.parent().removeClass();
        lastName.next().removeClass();
        lastName.parent().addClass('form-group has-feedback has-success');
        lastName.next().addClass('glyphicon form-control-feedback glyphicon-ok');
        return true;
    }
}

function registrationCheckEmail(inputField){
    var email = $('form[name="accountRegistrationFrom"]').find('input[name='+ inputField +']');

    //if email == ""
    if(email.val()== ""){
        email.prev().html("&nbsp;");
        email.parent().removeClass();
        email.next().removeClass();
    }
    //if email < 5
    else if(email.val().length < 5){
        email.prev().html("Invalid e-mail address");
        email.parent().removeClass();
        email.next().removeClass();
        email.parent().addClass('form-group has-feedback has-error');
        email.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }
    else if(!isValidEmailAddress(email.val())){
        email.prev().html("Invalid e-mail address");
        email.parent().removeClass();
        email.next().removeClass();
        email.parent().addClass('form-group has-feedback has-error');
        email.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }
    else {
        email.prev().html("&nbsp;");
        email.parent().removeClass();
        email.next().removeClass();
        email.parent().addClass('form-group has-feedback has-success');
        email.next().addClass('glyphicon form-control-feedback glyphicon-ok');
        return true;
    }
}
function registrationCheckEmailVerify(email1, email2){
    var email = $('form[name="accountRegistrationFrom"]').find('input[name='+ email1 +']');
    var emailVerify = $('form[name="accountRegistrationFrom"]').find('input[name='+ email2 +']');

    //email Verify is blank
    if(emailVerify.val() == ""){
        emailVerify.prev().html("&nbsp;");
        emailVerify.parent().removeClass();
        emailVerify.next().removeClass();

    //emails don't match
    }else if(email.val() != emailVerify.val()) {
        email.prev().html("e-mail addresses don't match");
        email.parent().removeClass();
        email.next().removeClass();
        email.parent().addClass('form-group has-feedback has-error');
        email.next().addClass('glyphicon form-control-feedback glyphicon-remove');

        emailVerify.prev().html("e-mail addresses don't match");
        emailVerify.parent().removeClass();
        emailVerify.next().removeClass();
        emailVerify.parent().addClass('form-group has-feedback has-error');
        emailVerify.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }

    //something is wrong with the emails
    else if(!registrationCheckEmail(email1) || !registrationCheckEmail(email2)){
        //running though the functions in the else if will set the appropriate error messages
    }

    //success
    else {
        return true;
    }
}
function registrationCheckPassword(inputField){
    var password = $('form[name="accountRegistrationFrom"]').find('input[name='+ inputField +']');

    //if password == ""
    if(password.val()== ""){
        password.prev().html("&nbsp;");
        password.parent().removeClass();
        password.next().removeClass();
    }
    //if password < 8
    else if(password.val().length < 8){
        password.prev().html("Your password is too short");
        password.parent().removeClass();
        password.next().removeClass();
        password.parent().addClass('form-group has-feedback has-error');
        password.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }
    else {
        password.prev().html("&nbsp;");
        password.parent().removeClass();
        password.next().removeClass();
        password.parent().addClass('form-group has-feedback has-success');
        password.next().addClass('glyphicon form-control-feedback glyphicon-ok');
        return true;
    }
}



function registrationCheckPasswordVerify(password1, password2){
    var password = $('form[name="accountRegistrationFrom"]').find('input[name='+ password1 +']');
    var passwordVerify = $('form[name="accountRegistrationFrom"]').find('input[name='+ password2 +']');

    //passwordVerify is blank
    if(password.val() == ""){
        password.prev().html("&nbsp;");
        password.parent().removeClass();
        password.next().removeClass();

    //password don't match
    }else if(password.val() != passwordVerify.val()) {
        password.prev().html("Passwords don't match");
        password.parent().removeClass();
        password.next().removeClass();
        password.parent().addClass('form-group has-feedback has-error');
        password.next().addClass('glyphicon form-control-feedback glyphicon-remove');

        passwordVerify.prev().html("Passwords don't match");
        passwordVerify.parent().removeClass();
        passwordVerify.next().removeClass();
        passwordVerify.parent().addClass('form-group has-feedback has-error');
        passwordVerify.next().addClass('glyphicon form-control-feedback glyphicon-remove');
    }

    //something is wrong with the passwords
    else if(!registrationCheckPassword(password1) || !registrationCheckPassword(password2)){

        //running though the functions in the else if will set the appropriate error messages

    }

    //success
    else {
        return true;
    }
}

function isAlphanumeric(value){
    var letterNumber = /^[0-9a-zA-Z]+$/;
    if(value.match(letterNumber)){
        return true;
    }
    else{
        return false;
    }
}

function isValidEmailAddress(emailAddress) {
    var pattern = new RegExp(/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i);
    return pattern.test(emailAddress);
};