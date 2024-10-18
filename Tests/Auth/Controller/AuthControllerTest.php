<?php

namespace Auth\Controller;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tigrino\Auth\Controller\AuthController;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Database\Database;
use Tigrino\Core\Renderer\RendererInterface;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Router\RouterInterface;
use Tigrino\Core\Session\SessionManager;

class AuthControllerTest extends TestCase
{
    private Database $db;
    private UserRepository $repository;
    private RendererInterface $renderer;
    private ContainerInterface $container;
    private SessionManager $sessionManager;

    protected function setUp(): void
    {
        $this->renderer = $this->createMock(RendererInterface::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->sessionManager = $this->createMock(SessionManager::class);

        $this->db = new Database('sqlite');
        $this->repository = new UserRepository($this->db);

        $this->container->method('get')
            ->willReturnMap([
                [RendererInterface::class, $this->renderer],
                [UserRepository::class, $this->repository],
                [SessionManager::class, $this->sessionManager]
            ]);

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

        $user = new User('Tigrino', 'password');
        $this->repository->insert($user);
    }

    public function testResgisterPost(): void
    {
        $this->renderer->expects($this->once())
            ->method('render')
            ->willReturn('auth/register.html.twig');

        $authController = new AuthController($this->container);

        $request = new ServerRequest('POST', '/register');
        $request = $request->withParsedBody([
            'username' => 'Tigrino',
            'password' => 'password',
            'confirm_password' => 'password'
        ]);

        $response = $authController->execute('register', [], $request);
        $this->assertEquals(303, $response->getStatusCode());
        $this->assertEquals(['/login'], $response->getHeader('Location'));
    }

    public function testRegisterGet(): void
    {
        $this->renderer->expects($this->once())
            ->method('render')
            ->willReturn('auth/register.html.twig');

        $authController = new AuthController($this->container);

        $request = new ServerRequest('GET', '/register');

        $response = $authController->execute('register', [], $request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('auth/register.html.twig', (string)$response->getBody());
    }

    public function testLoginGet(): void
    {
        $this->renderer->expects($this->once())
            ->method('render')
            ->willReturn('auth/login.html.twig');

        $authController = new AuthController($this->container);

        $request = new ServerRequest('GET', '/register');

        $response = $authController->execute('login', [], $request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertStringContainsString('auth/login.html.twig', (string)$response->getBody());
    }

    public function testLoginPost(): void
    {
        $authController = new AuthController($this->container);
        $request = new ServerRequest('POST', '/login');
        $request = $request->withParsedBody([
            'username' => 'Tigrino',
            'password' => 'password'
        ]);

        $response = $authController->execute('login', [], $request);
        $this->assertEquals(200, $response->getStatusCode());
    }
}
