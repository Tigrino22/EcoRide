<?php

namespace Errors;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tigrino\Core\App;
use Tigrino\Core\Renderer\RendererInterface;
use Tigrino\Core\Router\RouterInterface;
use Tigrino\Errors\ErrorModule;

class ErrorModuleTest extends TestCase
{
    private App $app;
    private ContainerInterface $container;
    private RendererInterface $renderer;
    private RouterInterface $router;

    protected function setUp(): void
    {
        // Création de mocks
        $this->app = $this->createMock(App::class);
        $this->container = $this->createMock(ContainerInterface::class);
        $this->renderer = $this->createMock(RendererInterface::class);
        $this->router = $this->createMock(RouterInterface::class);

        // Mock du router
        $this->app->method('getRouter')->willReturn($this->router);
    }

    public function testConstructorAddsRoutesAndPaths(): void
    {
        // Mock pour vérifier que les routes sont ajoutées
        $this->router
            ->expects($this->once())
            ->method('addRoutes')
            ->with($this->equalTo(include __DIR__ . '/../../src/Errors/Config/Routes.php'));

        // Mock pour vérifier que les chemins des templates sont ajoutés
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(RendererInterface::class)
            ->willReturn($this->renderer);

        // Instanciation du module
        new ErrorModule($this->app, $this->container);
    }
}
