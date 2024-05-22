<?php
require_once dirname(__DIR__).'/src/Models/Operation.php';
use PHPUnit\Framework\TestCase;

class OperationTest extends TestCase
{
    private $pdo;
    private $operation;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->operation = new Operation();
        $this->operation->setConnection($this->pdo); 
    }

    public function testGetAllByType()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $type = 'some_type';
        $stmt = $this->createMock(PDOStatement::class);
        
        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->with($this->equalTo("SELECT id, type, cost FROM Operation WHERE REPLACE(LOWER(`type`), ' ', '_') = ?"))
                  ->willReturn($stmt);
        
        $stmt->expects($this->once())
             ->method('execute')
             ->with([$type]);
        
        $stmt->expects($this->once())
             ->method('fetch')
             ->willReturn(['id' => 1, 'type' => 'some_type', 'cost' => 100]);

        $result = $this->operation->getAllByType($type);
        $this->assertEquals(['id' => 1, 'type' => 'some_type', 'cost' => 100], $result);
    }

    public function testGetTotal()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $stmt = $this->createMock(PDOStatement::class);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->willReturn($stmt);

        $stmt->expects($this->once())
             ->method('execute');

        $stmt->expects($this->once())
             ->method('rowCount')
             ->willReturn(10);

        $this->operation->sqlTotal = "SELECT * FROM Operation"; 
        $result = $this->operation->getTotal();
        $this->assertEquals(10, $result);
    }

    public function testGetAll()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $filters = ['type' => 'some_type'];
        $page = 0;
        $rowsPerPage = 10;
        $order = 'asc';
        $orderBy = 'id';

        $stmt = $this->createMock(PDOStatement::class);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->willReturn($stmt);

        $stmt->expects($this->once())
             ->method('execute')
             ->with(['some_type']);

        $stmt->expects($this->once())
             ->method('fetchAll')
             ->willReturn([
                 ['id' => 1, 'type' => 'some_type', 'cost' => 100],
                 ['id' => 2, 'type' => 'some_type', 'cost' => 200]
             ]);

        $result = $this->operation->getAll($filters, $page, $rowsPerPage, $order, $orderBy);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(2, $result[1]['id']);
    }
}
