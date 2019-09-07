<?php

class DbOperation {
    private $conn;

    function __construct() {
        require_once dirname(__FILE__) . '/Config.php';
        require_once dirname(__FILE__) . '/DbConnect.php';

        $db = new DbConnect();
        $this->conn = $db->connect();
    }

    public function createUser($username, $pass, $email, $firstname, $lastname, $bottlesize) {
        if (!$this->userExists($username, $email)) {
            $password = md5($pass);
            $api_key = $this->generateApiKey();
            $stmt = $this->conn->prepare('insert into users (username, password, email, firstname, lastname, bottlesize, api_key) values (?,?,?,?,?,?,?)');
            $stmt->bind_param('sssssds', $username, $password, $email, $firstname, $lastname, $bottlesize,$api_key);
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
        $stmt = $this->conn->prepare('select * from users where username = ? and password = ?');
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    public function getUser($username) {
        $stmt = $this->conn->prepare('select * from users where username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    public function logRefill($username, $amount, $api_key) {
        if ($this->isValidApiKey($username, $api_key)) {
            $user = $this->getUser($username);
            $id = $user['id'];
            $stmt = $this->conn->prepare('insert into refills (user_id, amount) values (?, ?)');
            $stmt->bind_param('id', $id, $amount);
            $stmt->execute();
            $stmt->close();
            return REFILL_LOGGED;
        }
        return INVALID_API_KEY;
    }

    public function getRefills($username, $api_key) {
        if ($this->isValidApiKey($username, $api_key)) {
            $user = $this->getUser($username);
            $id = $user['id'];
            $stmt = $this->conn->prepare('select coalesce(sum(amount),0) as n_refills from refills where user_id = ?');
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $result_assoc = $result->fetch_assoc();
            $stmt->close();
            return $result_assoc;
        }
        return INVALID_API_KEY;
    }

    public function addLocation($name, $description, $lat, $long, $username, $api_key) {
        if ($this->isValidApiKey($username, $api_key)) {
            $user = $this->getUser($username);
            $id = $user['id'];
            $stmt = $this->conn->prepare('insert into refill_locations (user_id, location_name, description, latitude, longitude) values (?, ?, ?, ?, ?)');
            $stmt->bind_param("issdd", $id, $name, $description, $lat, $long);
            $stmt->execute();
            $stmt->close();
            return LOCATION_ADDED;
        }
        return INVALID_API_KEY;
    }

    public function getLocations($username, $api_key) {
        if ($this->isValidApiKey($username, $api_key)) {
            $stmt = $this->conn->prepare('select refill_locations.location_name,refill_locations.description, refill_locations.latitude, refill_locations.longitude, users.username from refill_locations left join users on users.id=refill_locations.user_id;');
            $stmt->execute();
            $result = $stmt->get_result();
            $result_assoc = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $result_assoc;
        }
        return INVALID_API_KEY;
    }

    private function isValidApiKey($username, $api_key) {
        $stmt = $this->conn->prepare('select * from users where username = ? and api_key = ?');
        $stmt->bind_param('ss', $username, $api_key);
        $stmt->execute();
        $stmt->store_result();
        $num_rows = $stmt->num_rows;
        $stmt->close();
        return $num_rows > 0;
    }

    private function userExists($username, $email) {
        $stmt = $this->conn->prepare('select id from users where username = ? or email = ?');
        $stmt->bind_param('ss', $username, $email);
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