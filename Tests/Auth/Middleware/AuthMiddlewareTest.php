<?php

namespace Auth\Middleware;

use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tests\Core\Controllers\TestController;
use Tigrino\Auth\AuthModule;
use Tigrino\Auth\Config\Role;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Middleware\AuthMiddleware;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\App;
use Tigrino\Core\Database\Database;

class AuthMiddlewareTest extends TestCase
{
    private \DI\Container $container;
    private Database $db;
    private UserRepository $userRepository;
    private AuthMiddleware $authMiddleware;

    /**
     * @throws \Exception
     */
    protected function setUp(): void
    {
        $this->db = new Database('sqlite');
        $builder = new ContainerBuilder();
        $builder->addDefinitions(dirname(__DIR__, 3) . '/Config/Container.php');
        $this->container = $builder->build();
        $this->userRepository = new UserRepository($this->db);
        $this->authMiddleware = new AuthMiddleware($this->container, $this->userRepository);

        $this->db->execute('DROP TABLE IF EXISTS users_sessions');
        $this->db->execute('DROP TABLE IF EXISTS sessions');
        $this->db->execute('DROP TABLE IF EXISTS users');

        $this->db->execute('CREATE TABLE IF NOT EXISTS users (
            id BLOB PRIMARY KEY,
            username TEXT,
            email TEXT,
            password TEXT,
            last_login DATETIME
        )');

        $this->db->execute('CREATE TABLE IF NOT EXISTS sessions (
            session_id BLOB PRIMARY KEY,
            user_id BLOB,
            session_token TEXT,
            created_at DATETIME,
            expires_at DATETIME,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
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
    }

    protected function tearDown(): void
    {
        // Nettoyage après les tests
        $this->db->execute('DROP TABLE IF EXISTS sessions');
        $this->db->execute('DROP TABLE IF EXISTS users_roles');
        $this->db->execute('DROP TABLE IF EXISTS roles');
        $this->db->execute('DROP TABLE IF EXISTS users');
    }

    public function testConstruct()
    {
        $middleware = new AuthMiddleware($this->container, $this->userRepository);
        $this->assertInstanceOf(AuthMiddleware::class, $middleware);
    }

//    public function testProcessWithValidTokenAndRole()
//    {
//        // Ajouter un utilisateur
//        $user = new User('test_user', 'password123', 'test@example.com', [Role::USER]);
//        $this->userRepository->insert($user);
//
//        $route = ['GET', '/test', [TestController::class, 'testAction'], 'test.authmiddleware', [Role::USER] ];
//        $app = new App($this->container, modules: [AuthModule::class]);
//        $app->getRouter()->addRoutes($route);
//
//        // Créer un token de session et l'associer à l'utilisateur
//        $session_token = bin2hex(random_bytes(32));
//        $this->userRepository->setSessionToken($user, $session_token);
//
//        // Créer une requête avec le cookie session_token
//        $request = (new ServerRequest('GET', '/test'))
//            ->withCookieParams(['session_token' => $session_token]);
//
//        $response = $app->run($request);
//
//        // Vérifier que le middleware laisse passer la requête (200 OK)
//        $this->assertEquals(200, $response->getStatusCode());
//        $this->assertEquals('OK', (string) $response->getBody());
//    }
//
//    public function testProcessWithInvalidTokenAndRole()
//    {
//        // Ajouter un utilisateur
//        $user = new User('test_user', 'password123', 'test@example.com', [Role::GUEST]);
//        $this->userRepository->insert($user);
//
//        $route = ['GET', '/test', [TestController::class, 'testAction'], 'test.authmiddleware', [Role::USER] ];
//        $app = new App($this->container, modules: [AuthModule::class]);
//        $app->getRouter()->addRoutes($route);
//
//        // Créer un token de session et l'associer à l'utilisateur
//        $session_token = bin2hex(random_bytes(32));
//        $this->userRepository->setSessionToken($user, $session_token);
//
//        // Créer une requête avec le cookie session_token
//        $request = (new ServerRequest('GET', '/test'))
//            ->withCookieParams(['session_token' => $session_token]);
//
//        $response = $app->run($request);
//
//        // Vérifier que le middleware bloque la requete (403)
//        $this->assertEquals(403, $response->getStatusCode());
//    }
}
