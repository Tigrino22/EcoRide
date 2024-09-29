<?php

namespace Tests\core\Database;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Tigrino\Core\Database\Database;

class DatabaseTest extends TestCase
{
    private Database $db;

    protected function setUp(): void
    {

        $dotenv = Dotenv::createUnsafeImmutable(dirname((__DIR__), 3));
        $dotenv->load();

        // Crée une instance de la classe Database
        $this->db = new Database();

        // Préparer la base de données de test
        $this->db->execute("CREATE DATABASE IF NOT EXISTS test_db");
        $this->db->execute("USE test_db");

        // Crée une table pour les tests
        $this->db->execute("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL
            )
        ");
    }

    protected function tearDown(): void
    {
        // Supprime la base de données de test
        $this->db->execute("DROP DATABASE IF EXISTS test_db");
    }

    public function testInsertAndQuery()
    {
        $this->db->execute("INSERT INTO users (name) VALUES (:name)", ['name' => 'John Doe']);

        $result = $this->db->query("SELECT * FROM users WHERE name = :name", ['name' => 'John Doe']);
        $this->assertCount(1, $result);
        $this->assertEquals('John Doe', $result[0]['name']);
    }

    public function testTransaction()
    {
        $this->db->beginTransaction();
        $this->db->execute("INSERT INTO users (name) VALUES (:name)", ['name' => 'Jane Doe']);
        $this->db->commit();

        $result = $this->db->query("SELECT * FROM users WHERE name = :name", ['name' => 'Jane Doe']);
        $this->assertCount(1, $result);
        $this->assertEquals('Jane Doe', $result[0]['name']);
    }

    public function testRollback()
    {
        $this->db->beginTransaction();
        $this->db->execute("INSERT INTO users (name) VALUES (:name)", ['name' => 'Rollback Test']);
        $this->db->rollBack();

        $result = $this->db->query("SELECT * FROM users WHERE name = :name", ['name' => 'Rollback Test']);
        $this->assertCount(0, $result);
    }

    public function testLastInsertId()
    {
        $this->db->execute("INSERT INTO users (name) VALUES (:name)", ['name' => 'Last Insert Test']);
        $lastId = $this->db->lastInsertId();

        $result = $this->db->query("SELECT * FROM users WHERE id = :id", ['id' => $lastId]);
        $this->assertCount(1, $result);
        $this->assertEquals('Last Insert Test', $result[0]['name']);
    }

    public function testGetConnection()
    {
        $conection = $this->db->getConnection();

        $this->assertInstanceOf(\PDO::class, $conection);
    }

    public function testQueryReturnFalseIfErrorGiven()
    {
        $result = $this->db->query("SELECT * FROM user WHERE name = ?", ['test']);

        $this->assertFalse($result);
    }
}
