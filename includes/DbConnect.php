<?php

class DbConnect {

    function __construct() {

    }

    function connect() {
        require_once 'Config.php';

        $conn = new mysqli(DB_HOST . ':' . DB_PORT, DB_USERNAME, DB_PASSWORD, DB_NAME);

        if (mysqli_connect_errno()) {
            echo 'Failed to connect to MySQL: ' . mysqli_connect_error();
        }

        return $conn;
    }
}