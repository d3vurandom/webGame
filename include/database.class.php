<?php
/**
 * I wrote this database class to make my life easier it handles all of my database transactions.
 */
class database extends mysqli{

    private $databaseUsername = 'asgard-web';
    private $databasePassword = "4495qwhYhqQacwRT";
    private $databaseName = 'asgard-web';
    private $databaseServerLocation = 'database.vpn.lan';

    // single instance of self shared among all instances
    private static $instance = null;

    //This method must be static, and must return an instance of the object if the object
    //does not already exist.
    public static function getInstance() {
        if (!self::$instance instanceof self) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    // The clone and wakeup methods prevents external instantiation of copies of the Singleton class,
    // this eliminates the possibility of duplicate objects.
    public function __clone() {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }
    public function __wakeup() {
        trigger_error('De-serializing is not allowed.', E_USER_ERROR);
    }
    /**
     * @param $sqlQuery
     * @param $parameters
     * @return bool|mysqli_stmt
     * this function takes in an sql Query and the parameters and then makes it into a prepared statement and executes it
     * then it return the result set.
     */
    public function databaseQuery($sqlQuery,$parameters){

        parent::__construct($this->databaseServerLocation, $this->databaseUsername, $this->databasePassword, $this->databaseName);
        if (mysqli_connect_error()) {
            exit('Connect Error (' . mysqli_connect_errno() . ') '
                . mysqli_connect_error());
        }
        parent::set_charset('utf-8');


        if(!is_array($parameters)){
            $parameters = array($parameters);
        }

        //prepare SQL statement
        if(!$statement = $this->prepare($sqlQuery)){
            echo "Prepare failed: (" . $this->errno . ") " . $this->error;
            return false;
        }
        if(count($parameters) >= 1){
            $parameterTypes = "";
            foreach($parameters as $parameter){

                if(is_float($parameter)){
                    $parameterTypes .= 'd';      // Double
                }
                elseif(is_int($parameter)){
                    $parameterTypes .= 'i';      // Integer
                }
                elseif(is_string($parameter)){
                    $parameterTypes .= 's';      // String
                }
                else{
                    $parameterTypes .= 'b';      // Blob and Unknown
                }
            }
            array_unshift($parameters, $parameterTypes);

            /* bind values */
            if(!call_user_func_array(array(&$statement,'bind_param'),$this->refValues($parameters))){
                echo "Binding parameters failed: (" . $statement->errno . ") " . $statement->error;
                return false;
            }
        }

        /* execute statement */
        if (!$statement->execute()) {
            echo "Execute failed: (" . $statement->errno . ") " . $statement->error;
            return false;
        }

        /* Store result */
        if (!$statement->store_result()) {
            echo "Store Result failed: (" . $statement->errno . ") " . $statement->error;
            return false;
        }

        return $statement;
    }
    /**
     * @param $statement
     * used for closing the statement once dones witht he returned values
     */
    public function closeDB($statement) {
        $statement->close();
        $this->close();
    }

    /**
     * @param $array
     * @return array
     * this is used to do some magic with references.
     */
    public function refValues($array){
        if (strnatcmp(phpversion(),'5.3') >= 0){ //Reference is required for PHP 5.3+
            $refs = array();
            foreach($array as $key => $value)
                $refs[$key] = &$array[$key];
            return $refs;
        }
        return $array;
    }
}
?>