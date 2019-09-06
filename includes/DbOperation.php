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
        echo "7<br>";
        $stmt->bind_param("s", $username);
        echo "8<br>";
        $stmt->execute();
        echo "9<br>";
        $result = $stmt->get_result();
        echo "10<br>";
        $user = $result->fetch_assoc();
        echo "11<br>";
        $stmt->close();
        echo "12<br>";
        return $user;
    }

    public function logRefill($username, $amount, $api_key) {
        if (isValidApiKey($username, $api_key)) {
            echo "6<br>";
            $user = $this->getUser($username);
            echo "13<br>";
            $id = $user["id"];
            #$stmt = $this->conn->prepare("insert into refills (user_id, amount) values (?, ?)");
            #$stmt->bind_param("id", $id, $amount);
            #$stmt->execute();
            #$stmt->close();
            return REFILL_LOGGED;
        }
        return INVALID_API_KEY;
    }

    private function isValidApiKey($username, $api_key) {
        $stmt = $this->conn->prepare("select * from users where username = ? and api_key = ?");
        echo "1<br>";
        $stmt->bind_param("ss", $username, $api_key);
        echo "2<br>";
        $stmt->execute();
        echo "3<br>";
        $stmt->store_result();
        echo "4<br>";
        $num_rows = $stmt->num_rows;
        $stmt->close();
        echo "5<br>";
        return $num_rows > 0;
    }

    private function userExists($username, $email) {
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