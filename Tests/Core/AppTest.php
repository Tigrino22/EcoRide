<?php

namespace Tests\Core;

use Dotenv\Dotenv;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Modules\TestModules;
use Tigrino\Core\App;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Router\RouterInterface;

class AppTest extends TestCase
{
    private App $app;

    public function setUp(): void
    {
        require_once dirname(__DIR__, 2) . "/Config/Config.php";

        $dotenv = Dotenv::createUnsafeImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        $this->app = new App([]);
    }


    // public function testAddSingleMiddleware(): void
    // {
    //     $middleware = function (ServerRequestInterface $request, $handler) {
    //         return new Response(200, [], 'Middleware exécuté');
    //     };

    //     $this->app->addMiddleware($middleware);

    //     $request = new ServerRequest("GET", "/");


    //     try {
    //         $response = $this->app->run($request);
    //     } catch (\Exception $exception) {
    //         echo $exception;
    //     }


    //     $this->assertInstanceOf(ResponseInterface::class, $response);
    //     $this->assertEquals(200, $response->getStatusCode());
    //     $this->assertEquals("Middleware exécuté", $response->getBody());

    //     restore_exception_handler();
    //     restore_error_handler();
    // }

    public function testAddMultipleMiddlewares()
    {
        $middleware1 = function (ServerRequestInterface $request, $handler) {
            return new Response(201, [], 'Premier middleware');
        };

        $middleware2 = function (ServerRequestInterface $request, $handler) {
            return new Response(202, [], 'Deuxième middleware');
        };

        $this->app->addMiddleware([$middleware1, $middleware2]);

        $request = new ServerRequest('GET', '/');

        $response = $this->app->run($request);

        // 2 here and 1 dispatch (lasted)
        $this->assertEquals(3, count($this->app->getMiddleware()));
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertEquals('Premier middleware', (string) $response->getBody());
    }

    public function testGetRouter()
    {
        // Vérifier que le getter retourne bien une instance de RouterInterface
        $this->assertInstanceOf(RouterInterface::class, $this->app->getRouter());
    }

    public function testAddModule()
    {
        $this->app = new App([TestModules::class]);

        // getMessage has to be set.
        $this->assertEquals("Ce module a été activé", $this->app->getModules()[0]->getMessage());
    }

    public function testDispatchMethodInRunMethod()
    {
        $route = [
            ["GET", "/test", function () {
                return new Response(200, [], 'Route dispatched');
            }]
        ];

        $router = new Router();
        $router->addRoutes($route);

        $routerReflection = new \ReflectionProperty(App::class, "router");
        $routerReflection->setValue($this->app, $router);

        $request = new ServerRequest("GET", "/test");

        $response = $this->app->run($request);

        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Route dispatched', (string)$response->getBody());
    }
}
