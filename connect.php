<?php
require_once('config.php');

class Connect { 
    public $connection;
 
    function __construct() 
    { 
       $this->connection = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME) or die("Unable to connect");
    }      

    function executeQuery($con,$sql) {
        $result = mysqli_query($con, $sql);
    }
} 

$newConnection = new Connect();
?>