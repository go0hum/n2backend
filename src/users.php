<?php
require_once dirname(__DIR__).'/src/core/bootstrap.php';
require_once $root.'/core/helper.php';
require_once $root.'/core/language/en.php';
require_once $root.'/core/Authorization.php';
require_once $root.'/Models/User.php';

$authorization = new Authorization();
$authorization->init();
$userJWT = $authorization->getUserInfo();

if ($method === 'GET' && isset($_GET['id'])) {
    $id = $_GET['id'] ?? null;
    if ($id === null) {
        Json(400, "error", $textApi[$lang]['id_is_required'], "MISSING_ID");
    }   
    try {
        Json(200, "success", $textApi[$lang]['operation_completed_successfully'], false, ["users" => (new User())->getById($id)]);
    } catch(PDOException $e) {
        Json(500, "error", $textApi[$lang]['error_retrievingt_the_records'] . $e->getMessage(), "MISSING_RECORDS");
    }
}

if ($method === 'GET') {
    $filters = $_GET['filters'] ?? [];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    $rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10;
    $order = $_GET['order'] ?? 'asc';
    $orderBy = ($_GET['orderBy'] && !empty($_GET['orderBy'])) ? $_GET['orderBy'] : 'id';
    try {
        $user = new User();
        Json(200, "success", $textApi[$lang]['operation_completed_successfully'], false, ["users" => $user->getAll($filters, $page, $rowsPerPage, $order, $orderBy), "total" => $user->getTotal()]);
    } catch(PDOException $e) {
        Json(500, "error", $textApi[$lang]['error_retrievingt_the_records'] . $e->getMessage(), "MISSING_RECORDS");
    }
}

if ($method === 'POST') {
    $params = json_decode(file_get_contents("php://input"), true);
    if (empty($params['username']) || empty($params['password']) || empty($params['status'])) {
        Json(400, "error", $textApi[$lang]['all_fields_are_mandatory'], "MISSING_FIELDS");
    }
    try {
        Json(201, "success", $textApi[$lang]['record_created_successfully'], false, ['id' => (new User())->add($params)]);
    } catch(PDOException $e) {
        Json(500, "error", $textApi[$lang]['error_creating_the_record'], "ERROR_CREATING_RECORD");
    }
}

if ($method === 'DELETE') {
    $id = $_GET['id'] ?? null;
    if ($id === null) {
        Json(400, "error", $textApi[$lang]['id_is_required'], "MISSING_ID");
    }
    try {
        if ((new User())->delete($id) > 0) {
            Json(200, "success", $textApi[$lang]['record_deleted'], false, '');
        } else {
            Json(404, "error", $textApi[$lang]['record_not_found'], "RECORD_NOT_FOUND");
        }
    } catch(PDOException $e) {
        Json(500, "error", $textApi[$lang]['error_deleting_record'] . $e->getMessage(), "ERROR_DELETING");
    }
}

if ($method === 'PUT') {
    $id = $_GET['id'] ?? null;
    if ($id === null) {
        Json(400, "error", $textApi[$lang]['id_is_required'], "MISSING_ID");
    }
    $params = json_decode(file_get_contents('php://input'), true);
    if (!isset($params['username']) || !isset($params['status'])) {
        Json(400, "error", $textApi[$lang]['all_fields_are_mandatory'], "MISSING_FIELDS");
    }
    try {  
        (new User())->update($id, $params);   
        Json(200, "success", $textApi[$lang]['user_update_successfully'], false,  "");
    } catch (Exception $e) {
        Json(404, "error", $textApi[$lang]['record_not_found'], "RECORD_NOT_FOUND");
    }
}
?>
