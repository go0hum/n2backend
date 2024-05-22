<?php
require_once dirname(__DIR__).'/src/core/bootstrap.php';
require_once $root.'/core/helper.php';
require_once $root.'/core/language/en.php';
require_once $root.'/core/Authorization.php';
require_once $root.'/Models/Record.php';

$authorization = new Authorization();
$authorization->init();
$userJWT = $authorization->getUserInfo();

if ($method === 'GET') {
    $filters = $_GET['filters'] ?? [];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    $rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10;
    $order = $_GET['order'] ?? 'asc';
    $orderBy = ($_GET['orderBy'] && !empty($_GET['orderBy'])) ? $_GET['orderBy'] : 'id'; 
    try {   
        $record = new Record();
        Json(200, "success", $textApi[$lang]['operation_completed_successfully'], false, ["records" => $record->getAll($userJWT->user_id, $filters, $page, $rowsPerPage, $order, $orderBy), "total" => $record->getTotal($userJWT->user_id)]);
    } catch(PDOException $e) {
        Json(500, "error", $textApi[$lang]['error_retrievingt_the_records'] . $e->getMessage(), "MISSING_RECORDS");
    }
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id === null) {
        Json(400, "error", $textApi[$lang]['id_is_required'], "MISSING_ID");
    }
    try {
        if ((new Record())->delete($id) > 0) {
            Json(200, "success", $textApi[$lang]['record_deleted'], false, '');
        } else {
            Json(404, "error", $textApi[$lang]['record_not_found'], "RECORD_NOT_FOUND");
        }
    } catch(PDOException $e) {
        Json(500, "error", $textApi[$lang]['error_deleting_record'] . $e->getMessage(), "ERROR_DELETING");
    }
}

if ($method === 'POST') {
    $params = json_decode(file_get_contents("php://input"), true);
    if (empty($params['credit']) || empty($params['type'])) {
        Json(400, "error", $textApi[$lang]['all_fields_are_mandatory'], "MISSING_FIELDS");
    }
    try {
        (new Record())->add($userJWT->user_id, $params);
        Json(201, "success", $textApi[$lang]['record_created_successfully'], false, '');
    } catch(PDOException $e) {
        Json(500, "error", $textApi[$lang]['error_creating_the_record'], "ERROR_CREATING_RECORD");
    }
}
?>
