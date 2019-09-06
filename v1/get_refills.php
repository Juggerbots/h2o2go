<?php

require_once '../includes/DbOperation.php';
require_once '../includes/HelperFunctions.php';

$response = array();
$helper = new HelperFunctions();
$request_params = $_REQUEST;
$server = $_SERVER;
$headers = $helper->getallheaders($server);

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    $error_fields = $helper->verifyRequiredParams($request_params, array('username'));
    if (!$error_fields['error'] && isset($headers['Authorization'])) {
        $username = $_GET['username'];
        $api_key = $headers['Authorization'];

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
        $response['message'] = $error_fields['fields'];
    }
} else {
    $response['error'] = true;
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);