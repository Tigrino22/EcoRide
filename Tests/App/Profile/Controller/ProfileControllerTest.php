<?php

namespace App\Profile\Controller;

use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tests\Core\Renderer\FakeRenderer;
use Tigrino\App\Profile\Controller\ProfileController;
use Tigrino\App\Profile\Entity\UserEcoride;
use Tigrino\App\Profile\Repository\UserEcorideRepository;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Database\Database;
use Tigrino\Core\Errors\ErrorHandler;
use Tigrino\Core\Renderer\RendererInterface;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Services\FlashService;
use Tigrino\Services\SerializerService;

use function DI\autowire;

class ProfileControllerTest extends TestCase
{
    private ContainerInterface $container;
    private ProfileController $controller;
    private Database $db;
    private UserRepository $repository;
    private UserEcoride $user;
    private string $userId;
    private SessionManager $session;
    private $flashService;

    protected function setUp(): void
    {
        $this->db = new Database('sqlite');
        $this->repository = new UserRepository($this->db);

        // Réinitialisation des tables
        $this->db->execute('DROP TABLE IF EXISTS users_roles');
        $this->db->execute('DROP TABLE IF EXISTS roles');
        $this->db->execute('DROP TABLE IF EXISTS users');

        $this->db->execute('CREATE TABLE IF NOT EXISTS users (
            id BINARY(16) PRIMARY KEY,
            username TEXT,
            password TEXT,
            email TEXT,
            name TEXT,
            firstname TEXT,
            telephone TEXT,
            birthday TEXT,
            photo TEXT,
            address TEXT,
            is_passenger BOOLEAN,
            is_driver BOOLEAN,
            created_at TIMESTAMP,
            updated_at TIMESTAMP                    
        )');

        $this->db->execute('CREATE TABLE IF NOT EXISTS roles (
            id BLOB PRIMARY KEY,
            name TEXT,
            number INTEGER
        )');

        $this->db->execute('CREATE TABLE IF NOT EXISTS users_roles (
            user_id BINARY(16),
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

        // Instanciation de l'utilisateur avec des valeurs non nulles pour les propriétés requises
        $this->user = new UserEcoride([
            'username' => 'testuser',
            'password' => 'hashedpassword',
            'email' => 'testuser@example.com',
            'name' => 'TestName',
            'firstname' => 'TestFirstName',
            'telephone' => '0123456789',
            'birthday' => '1990-01-01',
            'address' => 'Test Address',
            'is_passenger' => true,
            'is_driver' => false,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $this->userId = $this->user->getId();

        $this->repository->insert($this->user);

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            RendererInterface::class => autowire(FakeRenderer::class),
            UserEcorideRepository::class => function (ContainerInterface $c) {
                return new UserEcorideRepository($this->db);
            },
            Router::class => autowire(Router::class),
        ]);
        $this->container = $builder->build();

        $this->session = new SessionManager();

        $this->session->set('user', (new SerializerService())->objectToArray($this->user));
        $this->flashService = new FlashService($this->session);
        $this->controller = new ProfileController($this->container);
    }

    public function testIndex()
    {
        /** @var Response $result */
        $result = $this->controller->index();

        $this->assertInstanceOf(Response::class, $result);
        $this->assertEquals(200, $result->getStatusCode());
    }

    public function testUpdateSuccess()
    {
        $request = (new ServerRequest('POST', '/profile/update/' . $this->userId))
            ->withAttribute('id', $this->userId)
            ->withParsedBody([
            'name' => 'NewName',
            'firstname' => 'NewFirstName',
            'email' => 'newemail@example.com',
            'telephone' => '0123456789'
        ]);

        $router = $this->container->get(Router::class);
        $router->addRoutes([
            ['POST', '/profile/update/[*:id]', [ProfileController::class, 'update'], 'update', []],
            ['GET', '/profile', [ProfileController::class, 'update'], 'profile', []],
        ]);

        $response = $router->dispatch($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNotEmpty($this->flashService->getMessages());
    }

    public function testUpdateValidationError()
    {
        // Préparer une requête avec des données invalides
        $request = (new ServerRequest('POST', '/profile/update/' . $this->userId))
            ->withAttribute('id', $this->userId)
            ->withParsedBody([
                'name' => '',
                'firstname' => '',
                'email' => 'invalid-email'
            ]);

        $router = $this->container->get(Router::class);
        $router->addRoutes([
            ['POST', '/profile/update/[*:id]', [ProfileController::class, 'update'], 'update', []],
            ['GET', '/profile', [ProfileController::class, 'update'], 'profile', []],
        ]);

        $response = $router->dispatch($request);

        $this->assertEquals(400, $response->getStatusCode());
        $this->assertNotEmpty($this->flashService->getMessages());
    }
}
