<?php

require_once '../includes/DbOperation.php';
require_once '../includes/HelperFunctions.php';

$response = array();
$helper = new HelperFunctions();
$request_params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if ($helper->verifyRequiredParams($request_params, array('username', 'api_key'))) {
        $username = $_GET['username'];
        $api_key = $_GET['api_key'];

        $db = new DbOperation();

        $result = $db->getRefills($username, $api_key);

        if ($result == INVALID_API_KEY) {
            $response['error'] = true;
            $response['message'] = 'Invalid API key.';
        } else {
            $response['error'] = false;
            $response['n_refills'] = $result['n_refills'];
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