<?php

namespace App\Profile\Controller;

use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;
use Tests\Core\Renderer\FakeRenderer;
use Tigrino\App\Profile\Controller\CarController;
use Tigrino\App\Profile\Entity\CarEntity;
use Tigrino\App\Profile\Repository\CarRepository;
use Tigrino\Core\Database\Database;
use Tigrino\Core\Errors\ErrorHandler;
use Tigrino\Core\Renderer\RendererInterface;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Services\FlashService;
use function DI\autowire;

class CarControllerTest extends TestCase
{
    private ContainerInterface $container;
    private CarController $controller;
    private Database $db;
    private CarRepository $repository;
    private SessionManager $session;
    private FlashService $flashService;
    private Router $router;
    private CarEntity $car;
    private string $userId;

    protected function setUp(): void
    {

        ErrorHandler::init();

        $this->db = new Database('sqlite');
        $this->repository = new CarRepository($this->db);

        $builder = new ContainerBuilder();
        $builder->addDefinitions([
            RendererInterface::class => autowire(FakeRenderer::class),
            CarRepository::class => function () {
                return $this->repository;
            },
            SessionManager::class => function () {
                return $this->session;
            },
            FlashService::class => function () {
                return $this->flashService;
            },
            Router::class => autowire(Router::class),
        ]);

        $this->container = $builder->build();
        $this->router = $this->container->get(Router::class);

        // Création des tables nécessaires
        $this->db->execute('DROP TABLE IF EXISTS cars');
        $this->db->execute('CREATE TABLE IF NOT EXISTS cars (
            id BINARY(16) PRIMARY KEY,
            user_id BINARY(16),
            brand TEXT,
            model TEXT,
            color TEXT,
            plate_of_registration TEXT,
            first_registration_at TEXT,
            places INTEGER,
            created_at TEXT,
            updated_at TEXT
        )');

        // Création d'un utilisateur pour la session
        $this->userId = Uuid::uuid4();
        $this->session = new SessionManager();
        $this->session->set('user', [
            'id' => Uuid::fromString($this->userId),
            'username' => 'testuser',
            'email' => 'test@example.com'
        ]);

        $this->flashService = new FlashService($this->session);

        // Création d'un véhicule pour les tests
        $this->car = CarEntity::fromArray([
            'id' => Uuid::uuid4(),
            'user_id' => Uuid::fromString($this->userId),
            'brand' => 'TestBrand',
            'model' => 'TestModel',
            'color' => 'Red',
            'plate_of_registration' => 'ABC-123',
            'first_registration_at' => date('Y-m-d H:i:s'),
            'places' => 4,
            'preferences' => '',
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $this->repository->insertCar($this->car);

        $this->controller = new CarController($this->container);
    }

    public function testShow()
    {
        $response = $this->controller->show();

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testInsertGet()
    {
        $request = new ServerRequest('GET', '/car/insert');
        $this->router->addRoutes(
            [
                ['GET', '/car/insert', [CarController::class, 'insert'], 'insert', []]
            ]
        );

        $response = $this->router->dispatch($request);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testInsertPostSuccess()
    {
        $request = (new ServerRequest('POST', '/car/insert'))
            ->withParsedBody([
                'brand' => 'NewBrand',
                'model' => 'NewModel',
                'color' => 'Blue',
                'plate_of_registration' => 'XYZ-789',
                'first_registration_at' => '2020-01-01',
                'places' => 5,
                'preferences' => ''
            ]);

        $this->router->addRoutes(
            [
                ['POST', '/car/insert', [CarController::class, 'insert'], 'insert', []],
                ['GET', '/car/show', [CarController::class, 'show'], 'car.show', []]
            ]
        );

        $response = $this->router->dispatch($request);

        $this->assertEquals(302, $response->getStatusCode());
        $this->assertNotEmpty($this->flashService->getMessages());
    }
}
