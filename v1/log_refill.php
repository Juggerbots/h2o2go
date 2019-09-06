<?php

require_once "../includes/DbOperation.php";
require_once "../includes/HelperFunctions.php";

$response = array();
$helper = new HelperFunctions();
$request_params = $_REQUEST;
try {
    $headers = apache_request_headers();
} catch (Exception $e) {
    echo $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($helper->verifyRequiredParams($request_params, array('username', 'amount')) && isset($headers['Authorization'])) {
        $username = $_POST['username'];
        $amount = $_POST['amount'];
        $api_key = $headers['Authorization'];

        $db = new DbOperation();

        $result = $db->log_refill($username, $amount, $api_key);

        if ($result == REFILL_LOGGED) {
            $response['error'] = false;
            $response['message'] = 'Refill logged successfully.';
        } elseif ($result == INVALID_API_KEY) {
            $response['error'] = true;
            $response['message'] = 'Invalid API key.';
        } else {
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