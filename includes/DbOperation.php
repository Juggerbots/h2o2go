<?php

class DbOperation {
    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . "/Config.php";
        require_once dirname(__FILE__) . "/DbConnect.php";

        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function createUser($username, $pass, $email, $firstname, $lastname, $bottlesize) {
        if (!$this->userExists($username, $email)) {
            $password = md5($pass);
            $api_key = $this->generateApiKey();
            $stmt = $this->conn->prepare("insert into users (username, password, email, firstname, lastname, bottlesize, api_key) values (?,?,?,?,?,?,?)");
            $stmt->bind_param("sssssds", $username, $password, $email, $firstname, $lastname, $bottlesize,$api_key);
            if ($stmt->execute()) {
                $stmt->close();
                return USER_CREATED;
            } else {
                $stmt->close();
                return USER_NOT_CREATED;
            }
        } else {
            $stmt->close();
            return USER_ALREADY_EXISTS;
        }
    }

    public function userLogin($username, $pass) {
        $password = md5($pass);
        $stmt = $this->conn->prepare("select * from users where username = ? and password = ?");
        $stmt->bind_param("ss", $username, $password);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function getUser($username) {
        $stmt = $this->conn->prepare("select * from users where username = ?");
        $stmt->bind_param("s", $username);
        $user = $stmt->store_result()->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function isValidApiKey($api_key) {
        $stmt = $this->conn->prepare("select * from users where api_key = ?");
        $stmt->bind_param("s", $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function userExists($username, $email) {
        $stmt = $this->conn->prepare("select id from users where username = ? or email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    private function generateApiKey() {
        return md5(uniqid(rand(), true));
    }
}