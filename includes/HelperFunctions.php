<?php

class HelperFunctions {
    function __construct() {
    
    }

    public function verifyRequiredParams($request_params, $required_fields) {
        foreach ($required_fields as $field) {
            if (!isset($request_params[$field]) || strlen(trim($request_params[$field])) <= 0) {
                return false;
            }
        }

        return true;
    }
}