<?php

require_once "../includes/DbOperation.php";
require_once "../includes/HelperFunctions.php";

$response = array();
$helper = new HelperFunctions();
$request_params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if ($helper->verifyRequiredParams($request_params, array('username', 'amount', 'api_key'))) {
        $username = $_POST['username'];
        $amount = $_POST['amount'];
        $api_key = $_POST['api_key'];

        $db = new DbOperation();

        $result = $db->logRefill($username, $amount, $api_key);

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

echo json_encode($response);