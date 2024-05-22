<?php
require_once dirname(__DIR__).'/core/bootstrap.php';
require_once dirname(__DIR__).'/core/db.php';

class User 
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

    public function setSqlTotal($sql)
    {
        $this->sqlTotal = $sql;
    }

    public function authenticateUser($username, $password)
    {
        $stmt = $this->conn->prepare("SELECT id FROM Users WHERE username=:username AND password=:password AND status = 1");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ? $user['id'] : false;
    }

    public function getById($id)
    {
        $sql = "SELECT id, username, status FROM Users WHERE id = ? LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
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
        $sql = "SELECT id, username, status FROM Users";
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

    public function add($data)
    {
        $sql = "INSERT INTO Users (username, password, status) VALUES (?, ?, ?)";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$data['username'], $data['password'], $data['status']]);
        return $this->conn->lastInsertId();
    }

    public function delete($id)
    {
        $sql = "UPDATE Users SET status = 0 WHERE id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->rowCount();
    }

    public function update($id, $params) 
    {
        $id = (int)$id;
        $username = $params['username'];
        $password = isset($params['password']) ? $params['password'] : null;
        $status = (int)$params['status'];
        if ($password) {
            $stmt = $this->conn->prepare('UPDATE Users SET username = ?, password = ?, status = ? WHERE id = ?');
            $stmt->execute([$username, $password, $status, $id]);
        } else {
            $stmt = $this->conn->prepare('UPDATE Users SET username = ?, status = ? WHERE id = ?');
            $stmt->execute([$username, $status, $id]);
        }
    }
}