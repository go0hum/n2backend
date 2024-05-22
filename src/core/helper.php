<?php

function Json($code, $status, $message, $errorCode=false, $rows=false) 
{
    http_response_code($code); 
    $data = [];
    $data["status"] = $status;
    $data["message"] = $message;
    if ($errorCode) {
        $data['error_code'] = $errorCode;
    } 
    if ($rows) {
        $data['data'] = $rows;
    }
    echo json_encode($data);
    
    if (getenv('APP_ENV') === 'production') {
        exit;
    }
}