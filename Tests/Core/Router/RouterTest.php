<?php

namespace Tests\Core\Router;

use DI\ContainerBuilder;
use DI\Container;
use Dotenv\Dotenv;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Tigrino\Auth\Config\Role;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Database\Database;
use Tigrino\Core\Router\Router;
use Tests\Core\Controllers\TestController;
use Tigrino\Auth\AuthModule;
use Tigrino\Auth\Middleware\AuthMiddleware;
use Tigrino\Core\App;
use Tigrino\Http\Response\JsonResponse;

class RouterTest extends TestCase
{
    /** @var Router */
    private $router;
    private Container $container;
    private Database $db;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $dotenv = Dotenv::createUnsafeImmutable(dirname(__DIR__, 3));
        $dotenv->load();

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(dirname(__DIR__, 2) . '/Config/Container.php');
        $this->container = $containerBuilder->build();

        $this->db = new Database('sqlite');
        $this->userRepository = new UserRepository($this->db);

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

        $this->router = new Router($this->container);
        $this->router->addRoutes([
            ["GET", "/test/show", [TestController::class, "index"], "test.show", []],
            ["GET", "/test/callable", function () {
                return new Response(200, [], "Hello test callable");
            }, "test.callable", []],
            ["GET", "/test/notfound", [TestController::class, "index"], "test.notfound", []],
            ["GET", "/test/[i:id]-[a:slug]", [TestController::class, "show"], "test.showWithId", []],
            ["POST", "/test/create", [TestController::class, "create"], "test.create", []]
        ]);
    }

    protected function tearDown(): void
    {
        $this->db->execute('DROP TABLE IF EXISTS users_roles');
        $this->db->execute('DROP TABLE IF EXISTS roles');
        $this->db->execute('DROP TABLE IF EXISTS users');
    }

    public function testGetRouteMatched()
    {
        // Simuler une requête GET sur /test
        $request = new ServerRequest('GET', '/test/show');

        // Dispatcher la requête et obtenir la réponse
        $response = $this->router->dispatch($request);

        // Vérifier que la réponse est correcte
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello test", (string)$response->getBody());
    }

    public function testGetRouteMatchedWithCallable()
    {
        // Simuler une requête GET sur /test
        $request = new ServerRequest('GET', '/test/callable');

        // Dispatcher la requête et obtenir la réponse
        $response = $this->router->dispatch($request);

        // Vérifier que la réponse est correcte
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello test callable", (string)$response->getBody());
    }

    public function testPostRouteNotFound()
    {
        // Simuler une requête POST sur /test
        $request = new ServerRequest('POST', '/test/notfound');

        // Dispatcher la requête et obtenir la réponse
        $response = $this->router->dispatch($request);

        // Vérifier qu'une erreur 404 est renvoyée
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("<h1>Page not found</h1>", (string)$response->getBody());
    }

    /**
     * @throws \Exception
     */
    public function testRouteWithParameter()
    {
        // Simuler une requête GET sur /test/123
        $request = new ServerRequest('GET', '/test/123-Tigrino');

        // Dispatcher la requête et obtenir la réponse
        $response = $this->router->dispatch($request);

        // Vérifier que la réponse est correcte
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello test 123-Tigrino", (string)$response->getBody());
    }


    public function testRouteNotFound()
    {
        // Aucune route n'est ajoutée
        $request = new ServerRequest('GET', '/non-existent');

        // Dispatcher la requête et obtenir la réponse
        $response = $this->router->dispatch($request);

        // Vérifier qu'une erreur 404 est renvoyée
        $this->assertEquals(404, $response->getStatusCode());
        $this->assertEquals("<h1>Page not found</h1>", (string)$response->getBody());
    }

    public function testProtectedRouteAccess()
    {
        $app = new App($this->container, [AuthModule::class]);
        $app->addMiddleware(new AuthMiddleware($this->container, $this->userRepository));

        $app->getRouter()->addRoutes([
            ["GET", "/admin", [TestController::class, "admin"], "admin.dashboard", [Role::ADMIN]],
        ]);

        // Simuler une requête avec un rôle insuffisant
        $request = new ServerRequest('GET', '/admin');
        $request = $request->withAttribute('user_role', Role::ADMIN); // Rôle insuffisant

        // Dispatcher la requête et obtenir la réponse
        $response = $app->run($request);

        // Vérifier que l'accès est refusé
        $this->assertEquals(403, $response->getStatusCode());
        $this->assertEquals("<h1>Accès interdit</h1>", (string)$response->getBody());
    }

    public function testAccessGrantedForProtectedRoute()
    {
        // Simuler une requête avec un rôle suffisant
        $request = new ServerRequest('GET', '/admin');
        $request = $request->withAttribute('user_role', Role::ADMIN); // Rôle suffisant

        $app = new App($this->container, [AuthModule::class]);
        $app->addMiddleware(new AuthMiddleware($this->container, $this->userRepository));
        $app->getRouter()->addRoutes([
            ["GET", "/admin", [TestController::class, "admin"], "admin.dashboard", [Role::ADMIN]],
        ]);

        // Dispatcher la requête et obtenir la réponse
        $response = $app->run($request);

        // Vérifier que la réponse est correcte
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals("Hello admin", (string)$response->getBody()); // Modifier selon la réponse attendue
    }

    public function testCreate()
    {
        $controller = new TestController($this->container);

        // Simuler une requête POST avec des données
        $request = new ServerRequest(
            'POST',
            '/test/create',
            [],
            json_encode(['title' => 'New Post', 'content' => 'This is a new post'])
        );
        $request = $request->withParsedBody(['title' => 'New Post', 'content' => 'This is a new post']);

        $response = $controller->create($request);

        // Vérifier que la réponse est de type JsonResponse
        $this->assertInstanceOf(JsonResponse::class, $response);

        // Vérifier le code de statut
        $this->assertEquals(200, $response->getStatusCode());

        // Vérifier le contenu JSON de la réponse
        $body = (string)$response->getBody();
        $expected = [
            'message' => 'Données reçues avec succès',
            'data' => ['title' => 'New Post', 'content' => 'This is a new post']
        ];
        $this->assertJsonStringEqualsJsonString(json_encode($expected), $body);
    }

    public function testGetRoutes()
    {
        $routes = $this->router->getRoutes();

        $this->assertEquals(5, count($routes));
    }

    public function testGenerateRoute()
    {
        $route = $this->router->generate('test.show');

        $this->assertEquals('/test/show', $route);

        $route = $this->router->generate('test.showWithId', ['id' => 1, 'slug' => 'val']);

        $this->assertEquals('/test/1-val', $route);
    }
}
