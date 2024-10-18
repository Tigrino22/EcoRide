<?php

namespace Admin;

use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Tigrino\App\Admin\AdminModule;
use Tigrino\Core\App;
use Tigrino\Core\Renderer\RendererInterface;
use Tigrino\Core\Router\RouterInterface;

class AdminModuleTest extends TestCase
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
            ->with($this->equalTo(include __DIR__ . '/../../src/App/Admin/Config/Routes.php'));

        // Mock pour vérifier que les chemins des templates sont ajoutés
        $this->container
            ->expects($this->once())
            ->method('get')
            ->with(RendererInterface::class)
            ->willReturn($this->renderer);

        // Instanciation du module
        new AdminModule($this->app, $this->container);
    }
}