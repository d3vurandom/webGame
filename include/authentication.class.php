<?php
    require_once('./include/database.class.php');

class authentication{

    /**
     * @param $cookieName
     * @param $cookieValue
     * @param int $seconds
     * @return bool returns true is the cookie was set successfully
     */
    public function createAuthenticationCookie($cookieName, $cookieValue, $seconds = 315360000){

        global $domainPath;

        //$expire is set to expire in 10 years by default. We will handel the auto logout time separately.
        $expire = time() + $seconds;
        if(setcookie($cookieName, $cookieValue, $expire, '/', $domainPath )){
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * @param $cookieName
     * @return bool Returnes true if the cookie was removed successfully
     */
    public function removeAuthenticationCookie($cookieName){
        global $domainPath;

        if(setcookie($cookieName, "", time(), '/', $domainPath )){
            return true;
        }
        else {
            return false;
        }
    }
    /**
     * @return bool
     */
    public function isAuthenticated() {
        require_once('./include/config.php');
        global $cookieName;

        $authenticationSuccess = false;

        if(isset($_COOKIE[$cookieName])){
            $token = $_COOKIE[$cookieName];
            if(authentication::verifyAuthenticationToken($cookieName,$token)){
                $authenticationSuccess = true;
            }
        }
        if(!$authenticationSuccess){
            authentication::removeAuthenticationCookie($cookieName);
            return false;
        }
        else{
            return true;
        }
    }

    public function createAuthenticationToken($userID){

        if(is_int($userID)){
            require_once('./include/config.php');
            global
            $tokenPadLength,
            $baseConvertTimestampValue,
            $baseConvertRandomValue,
            $baseConvertUserIDValue,
            $baseConvertIPAddressValue,
            $tokenSalt;

            //get user IP address
            $userIPAddress = ip2long($_SERVER["REMOTE_ADDR"]);

            //convert to a different base
            $timestampBase = base_convert(time(),10, $baseConvertTimestampValue);
            $randomBase = base_convert(rand(0,9999999999),10,$baseConvertRandomValue);
            $userIDBase = base_convert($userID,10,$baseConvertUserIDValue);
            $userIPAddressBase = base_convert($userIPAddress,10,$baseConvertIPAddressValue);

            $timestampBasePadded = str_pad($timestampBase, $tokenPadLength, "0", STR_PAD_LEFT);
            $randomBasePadded = str_pad($randomBase, $tokenPadLength, "0", STR_PAD_LEFT); //just used as filler
            $userIDBasePadded = str_pad($userIDBase, $tokenPadLength, "0", STR_PAD_LEFT);
            $userIPAddressBasePadded = str_pad($userIPAddressBase, $tokenPadLength, "0", STR_PAD_LEFT);

            //sha512 of salt just for fun
            $sha512OfSalt = hash('sha512', $tokenSalt);

            //sha1 of HTTP USER AGENT, this way we catch is the cookie is moved to a different browser type/version
            $sha1UserAgent = sha1($_SERVER['HTTP_USER_AGENT']);

            //hash of the data that we are going to validate.
            $data = $timestampBasePadded . $randomBasePadded . $userIDBasePadded . $sha512OfSalt . $userIPAddressBasePadded . $sha1UserAgent;
            $sha512 = hash('sha512', $data . $tokenSalt);

            //lets start with the hash of the userAgent
            $firstPart = substr($sha1UserAgent,0,$tokenPadLength);
            $secondPart = substr($sha1UserAgent,$tokenPadLength,strlen($sha1UserAgent));
            $token = $firstPart . $sha512OfSalt . $secondPart;

            //now lets add in the timestamp
            $firstPart = substr($token,0,$tokenPadLength);
            $secondPart = substr($token,$tokenPadLength,strlen($token));
            $token = $firstPart . $timestampBasePadded . $secondPart;

            //now lets add in the random number
            $firstPart = substr($token,0,($tokenPadLength + $baseConvertTimestampValue));
            $secondPart = substr($token,($tokenPadLength + $baseConvertTimestampValue),strlen($token));
            $token = $firstPart . $randomBasePadded . $secondPart;

            //now lets add in the userID
            $firstPart = substr($token,0,($tokenPadLength + $baseConvertRandomValue));
            $secondPart = substr($token,($tokenPadLength + $baseConvertRandomValue),strlen($token));
            $token = $firstPart . $userIDBasePadded . $secondPart;

            //now lets add in the Users IP address
            $firstPart = substr($token,0,($tokenPadLength + $baseConvertUserIDValue));
            $secondPart = substr($token,($tokenPadLength + $baseConvertUserIDValue),strlen($token));
            $token = $firstPart . $userIPAddressBasePadded . $secondPart;

            //now lets add in the hash of it all
            $firstPart = substr($token,0,($tokenPadLength + $baseConvertIPAddressValue));
            $secondPart = substr($token,($tokenPadLength + $baseConvertIPAddressValue),strlen($token));
            $token = $firstPart . $sha512 . $secondPart;

            return $token;
        }
        else{
            return false;
        }
    }

    /**
     * @param $cookieName
     * @param $token
     * @param bool $returnArray
     * @return array|bool return Array returns am array of all the values if they are requested or just true or false
     */
    public function verifyAuthenticationToken($cookieName,$token,$returnArray = false){

        require_once('./include/config.php');

        global
        $tokenPadLength,
        $baseConvertTimestampValue,
        $baseConvertRandomValue,
        $baseConvertUserIDValue,
        $baseConvertIPAddressValue,
        $tokenSalt,
        $maximumIdleTime;


        //lets make sure the token is a string
        $token = filter_var($token,FILTER_SANITIZE_STRING);

        //now start by striping out the sha512 hash
        $firstPart = substr($token,0,($tokenPadLength + $baseConvertIPAddressValue));
        $sha512 = substr($token,($tokenPadLength + $baseConvertIPAddressValue),128);
        $thirdPart = substr($token,($tokenPadLength + $baseConvertIPAddressValue + 128),(strlen($token)-($tokenPadLength + $baseConvertIPAddressValue + 128)));
        $token = $firstPart . $thirdPart;
        //echo "sha512[" . strlen($sha512) . "] = " . $sha512 . "<br>";

        //now lets strip out the users IP address
        $firstPart = substr($token,0,($tokenPadLength + $baseConvertUserIDValue));
        $userIPAddressBasePadded = substr($token,($tokenPadLength + $baseConvertUserIDValue),$tokenPadLength);
        $thirdPart = substr($token,($tokenPadLength + $baseConvertUserIDValue + $tokenPadLength),(strlen($token)-($tokenPadLength + $baseConvertUserIDValue + $tokenPadLength)));
        $token = $firstPart . $thirdPart;
        //echo "IP Address[" . strlen($userIPAddressBasePadded) . "] = " . $userIPAddressBasePadded . " = " . long2ip(base_convert($userIPAddressBasePadded,$baseConvertIPAddressValue, 10)) .  "<br>";

        //now lets strip out the users userID
        $firstPart = substr($token,0,($tokenPadLength + $baseConvertRandomValue));
        $userIDBasePadded = substr($token,($tokenPadLength + $baseConvertRandomValue),$tokenPadLength);
        $thirdPart = substr($token,($tokenPadLength + $baseConvertRandomValue + $tokenPadLength),(strlen($token)-($tokenPadLength + $baseConvertRandomValue + $tokenPadLength)));
        $token = $firstPart . $thirdPart;
        //echo "UserID[" . strlen($userIDBasePadded) . "] = " . $userIDBasePadded . " = " . base_convert($userIDBasePadded,$baseConvertUserIDValue, 10) .  "<br>";

        //now lets strip out the random number
        $firstPart = substr($token,0,($tokenPadLength + $baseConvertTimestampValue));
        $randomBasePadded = substr($token,($tokenPadLength + $baseConvertTimestampValue),$tokenPadLength);
        $thirdPart = substr($token,($tokenPadLength + $baseConvertTimestampValue + $tokenPadLength),(strlen($token)-($tokenPadLength + $baseConvertTimestampValue + $tokenPadLength)));
        $token = $firstPart . $thirdPart;
        //echo "Random Number[" . strlen($randomBasePadded) . "] = " . $randomBasePadded . " = " . base_convert($randomBasePadded,$baseConvertRandomValue, 10) .  "<br>";

        //now lets strip out the timestamp
        $firstPart = substr($token,0,$tokenPadLength);
        $timestampBasePadded = substr($token,$tokenPadLength,$tokenPadLength);
        $thirdPart = substr($token,$tokenPadLength +$tokenPadLength,(strlen($token) - ($tokenPadLength + $tokenPadLength)));
        $token = $firstPart . $thirdPart;
        //echo "Timestamp[" . strlen($timestampBasePadded) . "] = " . $timestampBasePadded . " = " . base_convert($timestampBasePadded,$baseConvertTimestampValue, 10) .   "<br>";

        //now lets strip out the SHA512 of the salt
        $firstPart = substr($token,0,$tokenPadLength);
        $sha512OfSalt = substr($token,$tokenPadLength,128);
        $thirdPart = substr($token,$tokenPadLength + 128,(strlen($token) - ($tokenPadLength + 128)));
        $token = $firstPart . $thirdPart;
        //echo "sha512OfSalt[" . strlen($sha512OfSalt) . "] = " . $sha512OfSalt .  "<br>";

        //now we know that what is left is the hash of the userAgent
        $sha1UserAgent = $token;
        //echo "sha1OfUserAgent[" . strlen($sha1UserAgent) . "] = " . $sha1UserAgent .  "<br>";

        //now lets build the data string to hash and make sure the token was not tampered with
        $data = $timestampBasePadded . $randomBasePadded . $userIDBasePadded . $sha512OfSalt . $userIPAddressBasePadded . $sha1UserAgent;

        //set some variables so you don't get too confused
        $cookieHash = $sha512;
        $hashOfData = hash('sha512', $data . $tokenSalt);

        $cookieIP = base_convert($userIPAddressBasePadded,$baseConvertIPAddressValue, 10);
        $currentIP = ip2long($_SERVER['REMOTE_ADDR']);

        $cookieTimestamp = base_convert($timestampBasePadded,$baseConvertTimestampValue, 10);
        $currentTimestamp = time();

        $cookieUserAgentHash = $sha1UserAgent;
        $userAgentHash = sha1($_SERVER['HTTP_USER_AGENT']);

        //not used
        //$cookieRandomNumber = base_convert($randomBasePadded,$baseConvertRandomValue, 10);

        $cookieUserID = base_convert($userIDBasePadded,$baseConvertUserIDValue, 10);

        //let make sure nothing fishy went on and make sure the hashes match first
        if($cookieHash != $hashOfData){
            return false;
        }
        elseif($cookieUserAgentHash != $userAgentHash) {
            return false;
        }
        else if($cookieIP != $currentIP) {
            return false;
        }
        else if($cookieTimestamp < $currentTimestamp - $maximumIdleTime){
            return false;
        }
        else {

            //update the token and add a new cookie token
            $token = authentication::createAuthenticationToken((int)$cookieUserID);
            if(authentication::createAuthenticationCookie($cookieName, $token)){

                if($returnArray){

                    $ipAddress = long2ip(base_convert($userIPAddressBasePadded,$baseConvertIPAddressValue, 10));
                    $userID = base_convert($userIDBasePadded,$baseConvertUserIDValue, 10);
                    $randomNumber = base_convert($randomBasePadded,$baseConvertRandomValue, 10);
                    $timestamp = base_convert($timestampBasePadded,$baseConvertTimestampValue, 10);

                    $array = array(
                        'sha512'=>$sha512,
                        'ipAddress'=>$ipAddress,
                        'userID'=>$userID,
                        'randomNumber'=>$randomNumber,
                        'timestamp'=>$timestamp,
                        'sha512OfSalt'=>$sha512OfSalt,
                        'SHA1OfUserAgent'=>$sha1UserAgent
                    );
                    return $array;
                }

                return true;
            }else {

                return false;
            }

            return true;
        }
    }

    public function getUserIDFromToken(){
        include('./include/config.php');
        $token = $_COOKIE[$cookieName];
        $array = authentication::verifyAuthenticationToken($cookieName,$token,TRUE);
        return $array['userID'];
    }


    public function displayCookieInfo(){

        global $cookieName, $maximumIdleTime;
        $token = $_COOKIE[$cookieName];


        $cookeData = authentication::verifyAuthenticationToken($cookieName,$token,true);

        //let make sure nothing fishy went on
        if($cookeData){
            echo "<h1>Hash Match!</h1>";

            if($_SERVER["REMOTE_ADDR"] == $cookeData['ipAddress']){
                echo "<h1>IP addresses Match!</h1>";
                echo "Cookie___IP = " . $cookeData['ipAddress']  . "<br>";
                echo "Actual____IP = " . $_SERVER["REMOTE_ADDR"] . "<br>";
            }
            else {
                echo "remote address does not match<br>";
            }

            if(sha1($_SERVER['HTTP_USER_AGENT']) == $cookeData['SHA1OfUserAgent']){
                echo "<h1>SHA1 of user agent Match!</h1>";
                echo "Cookie___SHA1 user agent = " . $cookeData['SHA1OfUserAgent'] . "<br>";
                echo "Actual____SHA1 user agent = " . sha1($_SERVER['HTTP_USER_AGENT']) . "<br>";
            }
            else {
                echo "user agent does not match<br>";
            }

            if($cookeData['timestamp'] >= (time() - $maximumIdleTime)){

                echo "<h1>Time difference in seconds</h1>";
                echo "Cookie___Time = " . $cookeData['timestamp'] . "<br>";
                echo "Actual____Time = " . time() . "<br>";
                echo "difference in seconds = " .  (time() - $cookeData['timestamp']) . "<br>";
            }
            else {
                echo "<h1>Time difference in seconds</h1>";
                echo "Cookie___Time = " . $cookeData['timestamp'] . "<br>";
                echo "Actual___Time = " . time() . "<br>";
                echo "<b>difference in seconds > " .$maximumIdleTime . "</b> = " .  (time() - $cookeData['timestamp']) . "<br>";
            }
        }
        else {
            echo "Hashes do not match, cookie was tampered with";
        }
    }
}
?>