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
    $myUserID =  authentication::getUserIDFromToken();
    global $maximumIdleTime;


    if(isset($currentPage)){
        if($currentPage == "home"){
            $browserTitle = "Home Page";
            $pageTitle = "Global Chat";
        }
        else if($currentPage == "game"){
            $browserTitle = "Game On!";
            $pageTitle = "Game On!";
        }
        else if($currentPage == "createGame"){
            $browserTitle = "New Game";
            $pageTitle = "Create a New Game";
        }
        else if($currentPage == "continueGame"){
            $browserTitle = "game";
            $pageTitle = "Choose an Existing Game";
        }
    }
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN"
    "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
    <title><?php echo $browserTitle ?></title>

    <?php echo "<script> var myUserID = " . $myUserID . "</script>"; ?>
    <?php echo "<script> var maximumAuthIdleTime = " . $maximumIdleTime . "</script>"; ?>
    <script src="./javascript/jQuery_1.11.0.js" type="text/javascript"></script>
    <script src="./javascript/login.js" type="text/javascript"></script>
    <script src="./javascript/authentication.js" type="text/javascript"></script>
    <script src="./javascript/chat.js" type="text/javascript"></script>
    <link href="./css/normalize_3.0.css" rel="stylesheet" type="text/css">
    <link href="./css/bootstrap_3.1.1.css" rel="stylesheet" type="text/css">
    <link href="./css/style.css" rel="stylesheet" type="text/css">

</head>

<body>


<div id='headerContainer'>
    <div id='header'>
        <div id='navigation'>
            <ul>
                <li>
                    <a href="./home.php"><li <?php echo ($currentPage == "home") ? ('class="currentPage"') : '';?> >Global Chat</li></a>

                    <a href="./createGame.php"><li <?php echo ($currentPage == "createGame") ? ('class="currentPage"') : '';?> >New Game</li></a>

                    <li <?php echo ($currentPage == "existingGames") ? ('class="currentPage"') : '';?> >Existing Games</li>

                    <li <?php echo ($currentPage == "settings") ? ('class="currentPage"') : '';?> >Settings</li>

                    <a href="./logout.php"><li>Logout</li></a>
            </ul>
        </div>
        <div id="welcomeUser">
            <?php
                $firstName = user::getFirstNameByID($myUserID);
                $lastName = user::getLastNameByID($myUserID);
                echo "Welcome: " . $lastName . ", " . $firstName;
            ?>
        </div>

        <div id="countdownTimer">
        </div>

    </div>

</div>