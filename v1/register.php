<?php

require_once '../includes/DbOperation.php';
require_once '../includes/HelperFunctions.php';

$response = array();
$helper = new HelperFunctions();
$request_params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($helper->verifyRequiredParams($request_params, array('username', 'password', 'email', 'firstname', 'lastname', 'bottlesize'))) {
        $username = $_POST['username'];
        $password = $_POST['password'];
        $email = $_POST['email'];
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $bottlesize = $_POST['bottlesize'];

        $db = new DbOperation();

        $result = $db->createUser($username, $password, $email, $firstname, $lastname, $bottlesize);

        if ($result == USER_CREATED) {
            $user = $db->userExists($username, $email);
            $response['error'] = false;
            $response['message'] = "User created successfully.";
            echo $user;
        } elseif ($result == USER_ALREADY_EXISTS) {
            $response['error'] = true;
            $response['message'] = 'User already exists.';
        } elseif ($result == USER_NOT_CREATED) {
            $response['error'] = true;
            $response['message'] = 'Unknown error occurred.';
        }
    } else {
        $response['error'] = true;
        $response['message'] = 'Required parameters missing.';
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);