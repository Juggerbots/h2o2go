<?php

class HelperFunctions {
    function __construct() {
    
    }

    public function verifyRequiredParams($request_params, $required_fields) {
        $result = array();

        $result['error'] = false;
        $error_fields = '';

        foreach ($required_fields as $field) {
            if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
                $result['error'] = true;
                $error_fields .= $field . ', ';
            }
        }

        $result['fields'] = $error_fields;

        return $result;
    }

    public function getallheaders($server) {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
        }
}