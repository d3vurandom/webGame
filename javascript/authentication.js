function authenticationTimer(maximumIdleTime){

    //set time till de authentication
    var TimeTillDeAuth = ((new Date().getTime()/1000) + maximumIdleTime );

    // variables for time units
    var days, hours, minutes, seconds;

    // get tag element
    var countdown = document.getElementById("countdownTimer");

    setInterval(function () {

        // find the amount of "seconds" between now and target
        var current_date = (new Date().getTime()/1000);

        //seconds left on the timer
        var seconds_left = TimeTillDeAuth - current_date;

        // do some time calculations
        days = parseInt(seconds_left / 86400);
        seconds_left = seconds_left % 86400;

        hours = parseInt(seconds_left / 3600);
        seconds_left = seconds_left % 3600;

        minutes = parseInt(seconds_left / 60);
        seconds = parseInt(seconds_left % 60);

        // format countdown string + set tag value
        countdown.innerHTML = "You will be logged out in: " + days + "d, " + hours + "h, "
         + minutes + "m, " + seconds + "s";
        if(seconds_left <= 0){
                window.location.href = "index.php";
        }

    }, 500);
}