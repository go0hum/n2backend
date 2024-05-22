<?php
require_once dirname(__DIR__).'/core/bootstrap.php';
require_once dirname(__DIR__).'/core/db.php';

class Operation 
{
    private $conn;
    public $sqlTotal;

    public function __construct()
    {
        $this->conn = getDbConnection();
    }

    public function setConnection($pdo)
    {
        $this->conn = $pdo;
    }

    public function getAllByType($type)
    {
        $sql = "SELECT id, type, cost FROM Operation WHERE REPLACE(LOWER(`type`), ' ', '_') = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$type]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTotal()
    {
        $stmtTotal = $this->conn->prepare($this->sqlTotal);
        $stmtTotal->execute();
        return $stmtTotal->rowCount();
    }

    public function getAll($filters, $page, $rowsPerPage, $order, $orderBy)
    {
        $conditions = [];
        $values = [];
        foreach ($filters as $column => $value) {
            if ($value !== '') {
                $conditions[] = "$column = ?";
                $values[] = $value;
            }
        }
        $sql = "SELECT id, type, cost FROM Operation";
        $this->sqlTotal = $sql;
        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(" AND ", $conditions);
        }
        $sql .= " ORDER BY $orderBy $order";
        $offset = $page * $rowsPerPage;
        $sql .= " LIMIT $offset, $rowsPerPage";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}