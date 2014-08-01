<?php
    require_once('./include/authentication.class.php');
    require_once('./include/user.class.php');
    require_once('./include/game.class.php');
    require_once('./include/config.php');

    if(!authentication::isAuthenticated()){
        echo "You are not authenticated!";
        echo '<script>setTimeout(function(){
                  window.location = "./index.php";
                }, 1000);</script>';
        exit(0);
    }
    $currentPage = "createGame";
?>
<?php require_once('./include/header.php');?>
<div id='globalChatContainer'>
    <div id='globalChat'>
        <h3>Challenge a Player</h3>
        <hr>
        <?php

        ?>
    </div>
</div>
</div>
<footer>
    <?php include('./include/footer.php') ?>
</footer>
</body>
</html>