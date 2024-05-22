<?php
require_once dirname(__DIR__).'/src/core/bootstrap.php';
require_once $root.'/core/helper.php';
require_once $root.'/core/language/en.php';
require_once $root.'/core/Authorization.php';
require_once $root.'/core/db.php';
require_once $root.'/Models/User.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_GET['action']) && $_GET['action'] === 'login') {
    $auth = new Authorization();
    $data = json_decode(file_get_contents("php://input"));
    $userId = (new User())->authenticateUser($data->username, $data->password);
    if ($userId) {
        Json(200, "success", $textApi[$lang]['operation_completed_successfully'], false, $auth->getToken($userId, $data));
    } else {
        Json(401, "error", $textApi[$lang]['invalid_credentials'], "INVALID_CREDENTIALS");
    }
} 
Json(404, "error", $textApi[$lang]['route_not_found'], "ROUTE_NOT_FOUND");
