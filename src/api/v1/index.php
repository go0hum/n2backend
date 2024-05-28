<?php
require_once dirname(__DIR__).'/../core/bootstrap.php';
require_once dirname(__DIR__).'/../core/helper.php';
require_once dirname(__DIR__).'/../core/RandDotOrg.class.php';
require_once dirname(__DIR__).'/../core/language/en.php';
require_once dirname(__DIR__).'/../core/Authorization.php';
require_once dirname(__DIR__).'/../core/db.php';
require_once dirname(__DIR__).'/../Models/Operation.php';
require_once dirname(__DIR__).'/../Models/Record.php';
require_once dirname(__DIR__).'/../api/v1/OperationHandler.php';

$authorization = new Authorization();
$authorization->init();

if ($method === 'POST' && isset($_GET['action'])) {

    $action = $_GET['action'] ?? null;

    if ($action === null || empty($action)) {
        Json(400, "error", $textApi[$lang]['action_is_required'], "MISSING_ACTION");
    }

    $userJWT = $authorization->getUserInfo();
    $params = json_decode(file_get_contents('php://input'), true);

    try {
        $OperationData = (new Operation())->getAllByType($action);
        $cost = ($OperationData['cost'] * -1);
        $operationId = $OperationData['id'];
        $RecordData = (new Record())->getAllByOperationIdAndUserId($operationId, $userJWT->user_id);
        if ($RecordData) {
            $descount = $cost + $RecordData['user_balance'];
        } else {
            $descount = $cost;
        }
        if ($descount < 0) {
            Json(400, "error", $textApi[$lang]['the_users_balance_isnt_enough'], "REQUEST_COST");
        }

        $result = (new OperationHandler())->handle(strtolower(trim($action)), $params);
        (new Record())->insertAll($operationId, $userJWT->user_id, $cost, $descount, json_encode(["data" => $result]));

        if ($result !== false){
            Json(200, "success", $textApi[$lang]['operation_completed_successfully'], false, ["data" => $result]);
        } else {
            Json(400, "error", $textApi[$lang]['action_not_found'], "MISSING_ACTION");
        }
    } catch(PDOException $e) {
        Json(500, "error", $textApi[$lang]['error_retrievingt_the_records'] . $e->getMessage(), "MISSING_RECORDS");
    }
}

Json(400, "error", $textApi[$lang]['action_not_found'], "MISSING_ACTION");
