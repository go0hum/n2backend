<?php
require_once dirname(__DIR__).'/src/core/bootstrap.php';
require_once $root.'/core/helper.php';
require_once $root.'/core/language/en.php';
require_once $root.'/core/Authorization.php';
require_once $root.'/Models/Operation.php';

$authorization = new Authorization();
$authorization->init();

if ($method === 'GET') {
    $filters = $_GET['filters'] ?? [];
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
    $rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10;
    $order = $_GET['order'] ?? 'asc';
    $orderBy = ($_GET['orderBy'] && !empty($_GET['orderBy'])) ? $_GET['orderBy'] : 'id'; 
    try {
        $operation = new Operation();
        Json(200, "success", $textApi[$lang]['operation_completed_successfully'], false, ["operations" => $operation->getAll($filters, $page, $rowsPerPage, $order, $orderBy), "total" => $operation->getTotal()]);
    } catch(PDOException $e) {
        Json(500, "error", $textApi[$lang]['error_retrievingt_the_records'] . $e->getMessage(), "MISSING_RECORDS");
    }
}

?>
