<?php
require_once dirname(__DIR__).'/core/bootstrap.php';
require_once dirname(__DIR__).'/core/db.php';

class Record 
{
    private $conn;
    private $sqlTotal;

    public function __construct()
    {
        $this->conn = getDbConnection();
    }

    public function setConnection($pdo)
    {
        $this->conn = $pdo;
    }

    public function getAllByOperationIdAndUserId($operationId, $userId)
    {
        $sqlRecord = "SELECT id, amount, user_balance FROM Record WHERE operation_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1";
        $stmtRecord = $this->conn->prepare($sqlRecord);
        $stmtRecord->execute([$operationId, $userId]);
        return $stmtRecord->fetch(PDO::FETCH_ASSOC);
    }

    public function insertAll($operationId, $userId, $amount, $balance, $operation)
    {
        $sql = "INSERT INTO Record (operation_id, user_id, amount, user_balance, operation_response, `date`) 
        VALUES (?, ?, ?, ?, ?, now())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$operationId, $userId, $amount, $balance, $operation]);
    }

    public function setSqlTotal($sql)
{
    $this->sqlTotal = $sql;
}

    public function getTotal($userId)
    {
        $stmtTotal = $this->conn->prepare($this->sqlTotal);
        $stmtTotal->execute([$userId]);
        return $stmtTotal->rowCount();
    }

    public function getAll($userId, $filters, $page, $rowsPerPage, $order, $orderBy)
    {
        $conditions = [];
        $values = [];
        $values[] = $userId;
        foreach ($filters as $column => $value) {
            if ($value !== '') {
                $conditions[] = "$column = ?";
                $values[] = $value;
            }
        }
        
        $sql = "SELECT Record.id, Record.amount, Record.user_balance, Record.operation_response, 
        Users.username, Record.date, Operation.type
        FROM Record 
        INNER JOIN Users ON Users.id = Record.user_id
        INNER JOIN Operation ON Operation.id = Record.operation_id
        WHERE user_id = ?";

        $this->sqlTotal = $sql;

        if (!empty($conditions)) {
            $sql .= implode(" AND ", $conditions);
        }

        $sql .= " ORDER BY $orderBy $order";

        $offset = $page * $rowsPerPage;
        $sql .= " LIMIT $offset, $rowsPerPage";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($values);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function delete($id)
    {
        $sql = "SELECT r1.id, r1.amount, r1.user_balance
        FROM Record r1
        JOIN Record r2 ON r1.operation_id = r2.operation_id AND r1.user_id = r2.user_id
        WHERE r2.id = ?
        AND r1.id >= ?
        ORDER BY r1.id ASC";
        $stmtAll = $this->conn->prepare($sql);
        $stmtAll->execute([$id, $id]);
        $records = $stmtAll->fetchAll(PDO::FETCH_ASSOC);

        $i = 0;
        $sum = 0;
        while($i < count($records)) {
            if ($records[$i]['id'] != $id) {
                $sum = $sum + $records[$i]['amount'];
                $sql = "UPDATE Record SET user_balance = ? WHERE id = ?";
                $stmtUpdate = $this->conn->prepare($sql);
                $stmtUpdate->execute([$sum, $records[$i]['id']]);
            } else {
                $amount = $records[$i]['amount'] * -1;
                $balance = $records[$i]['user_balance'];
                $sum = $amount + $balance;
            }
            $i++;
        }

        $sql = "DELETE FROM Record WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    public function add($userId, $data)
    {
        $balance = 0;
        $total = 0;
        $sql = "SELECT SUM(amount) AS total
        FROM Record WHERE operation_id = ? AND user_id = ? ORDER BY id DESC LIMIT 1";
        $stmtSearch = $this->conn->prepare($sql);
        $stmtSearch->execute([$data['type'], $userId]);
        $record = $stmtSearch->fetch(PDO::FETCH_ASSOC);
        $total += intval($record['total']) ?? 0;
        $balance = $data['credit'] + $total;
        $sql = "INSERT INTO Record (operation_id, user_id, amount, user_balance, operation_response, `date`) VALUES (?, ?, ?, ?, '{}', NOW())";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$data['type'], $userId, $data['credit'], $balance]);
        return $this->conn->lastInsertId();
    }
}