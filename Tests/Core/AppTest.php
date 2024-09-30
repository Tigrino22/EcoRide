<?php

namespace Tests\Core;

use Dotenv\Dotenv;
use Tigrino\Core\App;
use DI\ContainerBuilder;
use GuzzleHttp\Psr7\Response;
use Tests\Modules\TestModules;
use PHPUnit\Framework\TestCase;
use Tigrino\Core\Router\Router;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Tigrino\Core\Router\RouterInterface;
use Psr\Http\Message\ServerRequestInterface;
use Tests\Core\Controllers\TestController;

class AppTest extends TestCase
{
    private App $app;
    private \DI\Container $container;

    public function setUp(): void
    {
        require_once dirname(__DIR__, 2) . "/Config/Config.php";

        $dotenv = Dotenv::createUnsafeImmutable(dirname(__DIR__, 2));
        $dotenv->load();

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(dirname(__DIR__, 2) . '/Config/Container.php');
        $this->container = $containerBuilder->build();

        $this->app = new App($this->container, []);
    }

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
        $this->app = new App($this->container, [TestModules::class]);

        // getMessage has to be set.
        $this->assertEquals("Ce module a été activé", $this->app->getModules()[0]->getMessage());
    }
}
