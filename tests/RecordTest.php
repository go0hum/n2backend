<?php
require_once dirname(__DIR__).'/src/Models/Record.php';
use PHPUnit\Framework\TestCase;

class RecordTest extends TestCase
{
    private $pdo;
    private $record;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->record = new Record();
        $this->record->setConnection($this->pdo); 
    }

    public function testGetAll()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $userId = 1;
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
             ->with([$userId, 'some_type']);

        $stmt->expects($this->once())
             ->method('fetchAll')
             ->willReturn([
                 ['id' => 1, 'type' => 'some_type', 'credit' => 100],
                 ['id' => 2, 'type' => 'some_type', 'credit' => 200]
             ]);

        $result = $this->record->getAll($userId, $filters, $page, $rowsPerPage, $order, $orderBy);
        $this->assertCount(2, $result);
        $this->assertEquals(1, $result[0]['id']);
        $this->assertEquals(2, $result[1]['id']);
    }

    public function testGetTotal()
    {
        $userId = 1;
        $pdo = $this->createMock(PDO::class);

        $stmt = $this->createMock(PDOStatement::class);

        $expectedRowCount = 10;

        $stmt->expects($this->once())
             ->method('rowCount')
             ->willReturn($expectedRowCount);

        $pdo->expects($this->once())
            ->method('prepare')
            ->willReturn($stmt);

        $record = new Record();
        $record->setSqlTotal("SELECT * FROM Record");
        $record->setConnection($pdo); 

        $result = $record->getTotal($userId); 

        $this->assertEquals($expectedRowCount, $result);
    }


    public function testDelete()
    {
        $idToDelete = 1;

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->method('execute')->willReturn(true); 
        $stmt->method('rowCount')->willReturn(1); 
        $this->pdo->method('prepare')->willReturn($stmt);

        $deletedRows = $this->record->delete($idToDelete);

        $this->assertEquals(1, $deletedRows);
    }

    public function testAdd()
    {
        $userId = 1;
        $data = [
            'type' => 1,
            'credit' => 100
        ];
        
        $expectedBalance = $data['credit'];

        $stmt = $this->createMock(PDOStatement::class);

        $stmt->expects($this->any())
            ->method('execute')
            ->willReturnCallback(function ($params) use ($data, $userId) {
                static $callCount = 0;
                $callCount++;
                
                if ($callCount == 1) {
                    return true; 
                } elseif ($callCount == 2) {
                    return true; 
                }
                
                return false;
            });

        $stmt->expects($this->any())
            ->method('fetch')
            ->willReturn(['total' => 0]);

        $this->pdo->expects($this->any())
                ->method('prepare')
                ->willReturn($stmt);

        $this->pdo->expects($this->once())
            ->method('lastInsertId')
            ->willReturn('123'); 
        
        $result = $this->record->add($userId, $data);
        
        $this->assertEquals('123', $result); 
    }

}
