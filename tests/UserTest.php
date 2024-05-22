<?php
require_once dirname(__DIR__).'/src/Models/User.php';
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $pdo;
    private $user;

    protected function setUp(): void
    {
        $this->pdo = $this->createMock(PDO::class);
        $this->user = new User();
        $this->user->setConnection($this->pdo);
    }

    public function testAuthenticateUser()
    {
        $username = 'testuser';
        $password = 'testpass';

        $stmt = $this->createMock(PDOStatement::class);

        $stmt->expects($this->any())
             ->method('bindParam')
             ->willReturn(true);

        $stmt->expects($this->once())
             ->method('execute')
             ->willReturn(true);

        $stmt->expects($this->once())
             ->method('fetch')
             ->willReturn(['id' => 1]);

        $this->pdo->method('prepare')
                  ->willReturn($stmt);

        $result = $this->user->authenticateUser($username, $password);

        $this->assertEquals(1, $result); 
    }


    public function testGetById()
    {
        $id = 1;
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with($this->equalTo([$id]));

        $stmt->expects($this->once())
             ->method('fetch')
             ->willReturn(['id' => 1, 'username' => 'testuser', 'status' => 1]);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->willReturn($stmt);

        $result = $this->user->getById($id);
        $this->assertEquals(['id' => 1, 'username' => 'testuser', 'status' => 1], $result);
    }

    public function testGetTotal()
    {
        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute');

        $stmt->expects($this->once())
             ->method('rowCount')
             ->willReturn(10);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->willReturn($stmt);

        $this->user->setSqlTotal("SELECT * FROM Users");
        $result = $this->user->getTotal();
        $this->assertEquals(10, $result);
    }

    public function testGetAll()
    {
        $filters = ['status' => 1];
        $page = 0;
        $rowsPerPage = 10;
        $order = 'asc';
        $orderBy = 'id';

        $stmt = $this->createMock(PDOStatement::class);
        $stmt->expects($this->once())
             ->method('execute')
             ->with($this->equalTo([1]));

        $stmt->expects($this->once())
             ->method('fetchAll')
             ->willReturn([['id' => 1, 'username' => 'testuser', 'status' => 1]]);

        $this->pdo->expects($this->once())
                  ->method('prepare')
                  ->willReturn($stmt);

        $result = $this->user->getAll($filters, $page, $rowsPerPage, $order, $orderBy);
        $this->assertEquals([['id' => 1, 'username' => 'testuser', 'status' => 1]], $result);
    }

     public function testAdd()
     {
     $data = ['username' => 'testuser', 'password' => 'password', 'status' => 1];
     $stmt = $this->createMock(PDOStatement::class);
     $stmt->expects($this->once())
          ->method('execute')
          ->with($this->equalTo(['testuser', 'password', 1]));

     $this->pdo->expects($this->once())
               ->method('prepare')
               ->willReturn($stmt);

     $this->pdo->expects($this->once())
               ->method('lastInsertId')
               ->willReturn('1'); 

     $result = $this->user->add($data);
     $this->assertEquals(1, $result);
     }

     public function testDelete()
     {
          $id = 1;
          $stmt = $this->createMock(PDOStatement::class);
          $stmt->expects($this->once())
               ->method('execute')
               ->with($this->equalTo([$id]));

          $stmt->expects($this->once())
               ->method('rowCount')
               ->willReturn(1);

          $this->pdo->expects($this->once())
                    ->method('prepare')
                    ->willReturn($stmt);

          $result = $this->user->delete($id);
          $this->assertEquals(1, $result);
     }

     public function testUpdate()
     {
          $id = 1;
          $params = ['username' => 'newuser', 'status' => 1];

          $stmt = $this->createMock(PDOStatement::class);
          $stmt->expects($this->once())
               ->method('execute')
               ->with($this->equalTo(['newuser', 1, $id]));

          $this->pdo->expects($this->once())
                    ->method('prepare')
                    ->willReturn($stmt);

          $this->user->update($id, $params);
     }
}
?>
