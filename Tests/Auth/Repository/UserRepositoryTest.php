<?php

namespace Tests\Auth\Repository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Database\Database;

class UserRepositoryTest extends TestCase
{
    private UserRepository $userRepository;
    private Database $database;

    protected function setUp(): void
    {
        $dotenv = Dotenv::createUnsafeImmutable(
            dirname(dirname(__DIR__, 2))
        );
        $dotenv->load();

        $this->database = new Database();
        $this->userRepository = new UserRepository();

        $this->database->execute(
            query: "DROP TABLE IF EXISTS users"
        );

        // Crée une table pour les tests
        $this->database->execute(query: "
            CREATE TABLE IF NOT EXISTS users (
            id CHAR(36) NOT NULL,
            username VARCHAR(60) NOT NULL,
            email VARCHAR(150) NOT NULL,
            password VARCHAR(60) NOT NULL,
            roles JSON NOT NULL,
            session_token VARCHAR(128),
            is_banned BOOLEAN DEFAULT FALSE,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            last_login DATETIME,
            primary key (id),
            unique (email),
            unique (username)
        )");
    }

    protected function tearDown(): void
    {
        // Supprime la base de données de test
        $this->database->execute("DROP TABLE IF EXISTS users");
    }

    public function testInsert()
    {
        $user = new User(
            id: 'test-uuid',
            username: 'testuser',
            password: 'password',
            roles: ['ROLE_USER'],
            email: 'test@example.com',
            sessionToken: null,
            lastLogin: null
        );

        $result = $this->userRepository->insert($user);

        $this->assertTrue($result);
    }

    public function testUpdate()
    {
        $user = new User(
            id: 'test-uuid',
            username: 'testuser',
            password: 'password',
            roles: ['ROLE_USER'],
            email: 'test@example.com',
            sessionToken: 'new-token',
            lastLogin: '2024-01-01 00:00:00'
        );

        $result = $this->userRepository->update($user);

        $this->assertTrue($result);
    }

    public function testFindByEmail()
    {
        $this->database->execute(query: "
            INSERT INTO users (id, username, email, password, roles, session_token, last_login)
            VALUES (
                    'test-uuid', 
                    'testuser', 
                    'test@example.com', 
                    'password', 
                    '[\"ROLE_USER\"]', 
                    'new-token', 
                    '2024-01-01 00:00:00'
                )
        ");

        $user = $this->userRepository->findByEmail('test@example.com');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test@example.com', $user->getEmail());
    }

    public function testFindByUsername()
    {
        $this->database->execute(query: "
            INSERT INTO users (id, username, email, password, roles, session_token, last_login)
            VALUES (
                    'test-uuid', 
                    'testuser', 
                    'test@example.com', 
                    'password', 
                    '[\"ROLE_USER\"]', 
                    'new-token', 
                    '2024-01-01 00:00:00'
                )
        ");

        $user = $this->userRepository->findByUsername('testuser');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('testuser', $user->getUsername());
    }

    public function testFindBySessionToken()
    {
        $this->database->execute(query: "
            INSERT INTO users (id, username, email, password, roles, session_token, last_login)
            VALUES (
                    'test-uuid', 
                    'testuser', 
                    'test@example.com', 
                    'password', 
                    '[\"ROLE_USER\"]', 
                    'new-token', 
                    '2024-01-01 00:00:00'
                )
        ");

        $user = $this->userRepository->findBySessionToken('new-token');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('new-token', $user->getSessionToken());
    }
}
