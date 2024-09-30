<?php

namespace Tigrino\Auth;

use Psr\Container\ContainerInterface;
use Tigrino\Auth\Middleware\AuthMiddleware;
use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;
use Tigrino\Core\Renderer\RendererInteface;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Router\RouterInterface;

class AuthModule implements ModuleInterface
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var RouterInterface
     */
    private $router;
    private ContainerInterface $container;

    /**
     * Méthode a implémenter en fonction du fonctionnement
     * du AuthModule.
     *
     * @param App $app
     * @return void
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->app = $container->get(App::class);
        $this->app->getRouter()->addRoutes(include __DIR__ . "/Config/Routes.php");

        $this->addAuthMiddleware();
    }

    private function addAuthMiddleware(): void
    {
        $this->app->addMiddleware(new AuthMiddleware($this->container->get(Router::class)));
    }
}
