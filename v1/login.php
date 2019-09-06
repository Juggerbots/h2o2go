<?php

require_once "../includes/DbOperation.php";
require_once "../includes/HelperFunctions.php";

$response = array();
$helper = new HelperFunctions();
$request_params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($helper->verifyRequiredParams($request_params,array('username','password'))) {
        $db = new DbOperation();

        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($db->userLogin($username, $password)) {
            $user = $db->getUser($username);

            $response['error'] = false;
            $response['username'] = $user['username'];
            $response['email'] = $user['email'];
            $response['firstname'] = $user['firstname'];
            $response['lastname'] = $user['lastname'];
            $response['bottlesize'] = $user['bottlesize'];
            $response['api_key'] = $user['api_key'];
        } else {
            $response['error'] = true;
            $response['message'] = 'Invalid username or password.';
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