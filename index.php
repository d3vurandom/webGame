<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Welcome!</title>
        <script src="./javascript/jQuery_1.11.0.js" type="text/javascript"></script>
        <script src="./javascript/bootstrap.js" type="text/javascript"></script>
        <script src="./javascript/login.js" type="text/javascript"></script>

        <link href="./css/normalize_3.0.css" rel="stylesheet" type="text/css">
        <link href="./css/bootstrap_3.1.1.css" rel="stylesheet" type="text/css">
        <link href="./css/style.css" rel="stylesheet" type="text/css">
        <meta charset="utf-8">
    </head>
    <body>
        <div id='loginBoxContainer'>
            <div id='loginBox'>
                <span class="center"><h4>Please login to continue</h4></span>
                <form name='loginForm'>
                    <table class='table loginTable control-group'>
                        <tr>
                            <td class="leftColumn">
                                <span class="h5">Username:</span>
                            </td>
                            <td class="rightColumn">
                                <input type="text" class='form-control' name="username" autocomplete="off" placeholder  = 'Username' tabindex=1>
                            </td>
                        </tr>
                        <tr>
                            <td class="leftColumn">
                                <span class="h5">Password:</span>
                            </td>
                            <td class="rightColumn">
                                <input type="password" class='form-control' name="password" autocomplete="off" placeholder  = 'Password' onkeyup="checkIfEnterKeyLogin(event)" tabindex=2>
                            </td>
                        </tr>
                    </table>
                </form>
                <table class='table loginTable'>
                    <tr>
                        <td class="leftColumn">
                            <button id='registerButton' class="btn btn-default" data-toggle="modal" data-target="#registrationModal" tabindex=4>
                                Register
                            </button>
                        </td>
                        <td class="rightColumn">
                            <button id='loginButton' class="btn btn-primary" value="Login" onclick="submitLogin()" onkeypress="submitLogin()" tabindex=3>
                                Login
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2">
                            <span class="center" id="loginErrorMessage"></span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        <!-- Registration Modal -->
        <div class="modal fade" id="registrationModal" tabindex="-1" role="dialog" aria-labelledby="registrationModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title" id="registrationModalLabel">Account Registration</h4>
                    </div>
                    <div class="modal-body">
                        <form name='accountRegistrationFrom'>
                            <table class="registerTable">
                                <tr>
                                    <td class="leftColumn">
                                        <span class="h5" >Username:</span>
                                    </td>
                                    <td class="rightColumn">
                                        <div class="form-group has-feedback">
                                            <label class="control-label"></label>
                                            <input type="text" class="form-control" name="username"
                                                   autocomplete="off" placeholder  = 'Username'
                                                   onblur="registrationCheckUsername()">
                                            <span class="form-control-feedback"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftColumn">
                                        <span class="h5" >First Name:</span>
                                    </td>
                                    <td class="rightColumn">
                                        <div class="form-group has-feedback">
                                            <label class="control-label"></label>
                                            <input type="text" class="form-control" name="firstName"
                                                   autocomplete="off" placeholder  = 'John'
                                                   onblur="registrationCheckFirstName()" >
                                            <span class="glyphicon form-control-feedback"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftColumn">
                                        <span class="h5" >Last Name:</span>
                                    </td>
                                    <td class="rightColumn">
                                        <div class="form-group has-feedback">
                                            <label class="control-label"></label>
                                            <input type="text" class="form-control" name="lastName"
                                                   autocomplete="off" placeholder  = 'Smith'
                                                   onblur="registrationCheckLastName()" >
                                            <span class="glyphicon form-control-feedback"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftColumn">
                                        <span class="h5" >E-mail Address:</span>
                                    </td>
                                    <td class="rightColumn">
                                        <div class="form-group has-feedback">
                                            <label class="control-label"></label>
                                            <input type="text" class="form-control" name="email"
                                                   autocomplete="off" placeholder  = 'john.smith@gmail.com'
                                                   onblur="registrationCheckEmail('email')" >
                                            <span class="glyphicon form-control-feedback"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftColumn">
                                        <span class="h5" >Verify E-mail:</span>
                                    </td>
                                    <td class="rightColumn">
                                        <div class="form-group has-feedback">
                                            <label class="control-label"></label>
                                            <input type="text" class="form-control" name="emailVerify"
                                                   autocomplete="off" placeholder  = 'john.smith@gmail.com'
                                                   onblur="registrationCheckEmailVerify('email','emailVerify')" >
                                            <span class="glyphicon form-control-feedback"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftColumn">
                                        <span class="h5" >Password:</span>
                                    </td>
                                    <td class="rightColumn">
                                        <div class="form-group has-feedback">
                                            <label class="control-label"></label>
                                            <input type="password" class="form-control" name="password"
                                                   autocomplete="off" placeholder  = 'Password'
                                                   onblur="registrationCheckPassword('password')" >
                                            <span class="glyphicon form-control-feedback"></span>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="leftColumn">
                                        <span class="h5" >Verify Password:</span>
                                    </td>
                                    <td class="rightColumn">
                                        <div class="form-group has-feedback">
                                            <label class="control-label"></label>
                                            <input type="password" class="form-control" name="passwordVerify"
                                                   autocomplete="off" placeholder  = 'Password'
                                                   onblur="registrationCheckPasswordVerify('password','passwordVerify')"
                                                   onkeyup="checkIfEnterKey_AccountRegistration(event)">
                                            <span class="glyphicon form-control-feedback"></span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal" tabindex=9>Cancel</button>
                        <button type="button" class="btn btn-primary" onclick="createAccount()" onkeypress="createAccount()">Create Account</button>
                    </div>
                </div>
            </div>
        </div>
        <footer>
            <?php include('./include/footer.php') ?>
        </footer>
    </body>
</html>

