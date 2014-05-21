<?php
    require_once('./include/config.php');
    require_once('./include/authentication.class.php');

    global $cookieName, $domainPath;

    echo $cookieName;
    if(authentication::removeAuthenticationCookie($cookieName)){
        echo "true";
    }
    else {
        echo "false";
    }

    header("Location: index.php");
    exit();
?>