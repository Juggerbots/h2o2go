<?php

require_once '../includes/DbOperation.php';
require_once '../includes/HelperFunctions.php';

$response = array();
$helper = new HelperFunctions();
$request_params = $_REQUEST;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $error_fields = $helper->verifyRequiredParams($request_params, array('name', 'description', 'lat', 'long', 'username', 'api_key'));
    if (!$error_fields['error']) {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $lat = $_POST['lat'];
        $long = $_POST['long'];
        $username = $_POST['username'];
        $api_key = $_POST['api_key'];

        echo $name;

        $db = new DbOperation();

        $result = $db->addLocation($name, $description, $lat, $long, $username, $api_key);

        if ($result == INVALID_API_KEY) {
            $response['error'] = true;
            $response['message'] = 'Invalid API key.';
        } elseif ($result == LOCATION_ADDED) {
            $response['error'] = false;
            $response['message'] = 'Location added successfully.';
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