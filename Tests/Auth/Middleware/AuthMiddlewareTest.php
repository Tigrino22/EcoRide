<?php

namespace Auth\Middleware;

use DI\ContainerBuilder;
use PHPUnit\Framework\TestCase;
use Tigrino\Auth\Config\Role;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Middleware\AuthMiddleware;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\App;
use Tigrino\Core\Database\Database;

class AuthMiddlewareTest extends TestCase
{
    private Database $db;
    private UserRepository $repository;
    private \DI\Container $container;
    private AuthMiddleware $authMiddleware;
    private App $app;

    protected function setUp(): void
    {
        $this->db = new Database('sqlite');
        $this->repository = new UserRepository($this->db);

        $this->db->execute('DROP TABLE IF EXISTS users_roles');
        $this->db->execute('DROP TABLE IF EXISTS roles');
        $this->db->execute('DROP TABLE IF EXISTS users');

        $this->db->execute('CREATE TABLE IF NOT EXISTS users (
            id BLOB PRIMARY KEY,
            username TEXT,
            email TEXT,
            password TEXT,
            last_login DATETIME
        )');

        $this->db->execute('CREATE TABLE IF NOT EXISTS roles (
            id BLOB PRIMARY KEY,
            name TEXT,
            number INTEGER
        )');

        $this->db->execute('CREATE TABLE IF NOT EXISTS users_roles (
            user_id BLOB,
            role_id BLOB,
            PRIMARY KEY (user_id, role_id)
        )');

        $this->db->execute(
            'INSERT INTO roles (id, name, number)
            VALUES 
                (?, "SUPERADMIN", 0),
                (?, "ADMIN", 1),
                (?, "USER", 2),
                (?, "GUEST", 3)',
            [
                hex2bin('08cc137eba2a42078f7202c7f859fea2'),  // Conversion en binaire
                hex2bin('284c4c6acb3349a2abb2bfa4083a59b2'),  // Conversion en binaire
                hex2bin('3bb93f51b0834fa9bd4b55e358b62e1c'),  // Conversion en binaire
                hex2bin('05f10bf37bec45128ae2d236b5786eab')   // Conversion en binaire
            ]
        );

        $user = new User('test_admin', 'password123', 'test@example.com', [Role::ADMIN]);
        $this->repository->insert($user);

        $user = new User('test_user', 'password123', 'test@example.com', [Role::USER]);
        $this->repository->insert($user);


        // Initialisation du middleware
        $containerBuilder = new ContainerBuilder();
        $this->container = $containerBuilder->build();
        $this->authMiddleware = new AuthMiddleware($this->container, $this->repository);

        // Création de l'application
        $this->app = new App($this->container);
    }

    public function testLogin()
    {
        $this->assertTrue(true);
    }
}
