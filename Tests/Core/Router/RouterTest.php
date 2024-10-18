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

    protected function setUp(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(dirname(__DIR__, 2) . '/Config/Container.php');
        $this->container = $containerBuilder->build();

        $this->router = new Router($this->container);
        $this->router->addRoutes([
            ["GET", "/test/show", [TestController::class, "index"], "test.show", []],
            ["GET", "/test/callable", function () {
                return new Response(200, [], "Hello test callable");
            }, "test.callable", []],
            ["GET", "/test/notfound", [TestController::class, "index"], "test.notfound", []],
            ["GET", "/test/[i:id]-[a:slug]", [TestController::class, "show"], "test.showWithId", []],
            ["POST", "/test/create", [TestController::class, "create"], "test.create", []],
            ["GET", "/404", [TestController::class, "notFoundTest"], "error.404", []],
            ["GET", "/protected", [TestController::class, "create"], "test.protected", [Role::ADMIN]],
        ]);
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

    public function testRouteNotFound()
    {
        // Simuler une requête POST sur /test
        $request = new ServerRequest('GET', '/noexist');

        // Dispatcher la requête et obtenir la réponse
        $response = $this->router->dispatch($request);

        // Redirection vers une page 404, comme erreur de la reponse instantaner 302
        // RedirectResponse
        $this->assertEquals(302, $response->getStatusCode());
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

    public function testCreate()
    {
        $controller = new TestController($this->container);

        $request = new ServerRequest(
            'POST',
            '/test/create',
            [],
            json_encode(['title' => 'New Post', 'content' => 'This is a new post'])
        );
        $request = $request->withParsedBody(['title' => 'New Post', 'content' => 'This is a new post']);

        $response = $controller->create($request);

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertEquals(200, $response->getStatusCode());

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

        // Nombre de routes dans le setUp
        $this->assertEquals(7, count($routes));
    }

    public function testGetProtectedRoutes()
    {
        $protectedRoutes = $this->router->getProtectedRoutes();

        $this->assertEquals(1, count($protectedRoutes));
        $this->assertEquals('test.protected', $protectedRoutes["/protected"]['name']);
    }

    public function testGenerateRoute()
    {
        $route = $this->router->generate('test.show');

        $this->assertEquals('/test/show', $route);

        $route = $this->router->generate('test.showWithId', ['id' => 1, 'slug' => 'val']);

        $this->assertEquals('/test/1-val', $route);
    }
}
