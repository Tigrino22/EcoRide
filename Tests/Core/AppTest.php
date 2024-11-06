<?php

namespace Tests\Core;

use Tests\Core\Controllers\TestController;
use Tests\Core\Middleware\FakeMiddleware;
use Dotenv\Dotenv;
use Tests\Core\Renderer\FakeRenderer;
use Tigrino\Core\App;
use DI\ContainerBuilder;
use Tests\Modules\TestModules;
use PHPUnit\Framework\TestCase;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Tigrino\Core\Renderer\RendererInterface;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Router\RouterInterface;

use function DI\autowire;

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
        $containerBuilder->addDefinitions(
            [
                FakeMiddleware::class => new FakeMiddleware(),
                Router::class => autowire(Router::class),
                RendererInterface::class => autowire(FakeRenderer::class),
            ]
        );
        $this->container = $containerBuilder->build();

        $this->app = new App($this->container, []);
    }

    public function testAddMultipleMiddlewares()
    {
        $this->app->addMiddleware([FakeMiddleware::class, FakeMiddleware::class]);

        $request = new ServerRequest('GET', '/');

        $this->container->get(Router::class)->addRoutes([
            ['GET', '/', [TestController::class, 'index'], 'index', []],
            ['GET', '/error.404', [TestController::class, 'notFoundTest'], 'error.404', []]
        ]);

        $response = $this->app->run($request);

        // 2 here and 1 dispatch (lasted)
        $this->assertEquals(3, count($this->app->getMiddleware()));
        $this->assertInstanceOf(ResponseInterface::class, $response);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('Hello test', (string) $response->getBody());
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
