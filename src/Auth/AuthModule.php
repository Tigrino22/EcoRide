<?php

namespace Tigrino\Auth;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tigrino\Auth\Middleware\AuthMiddleware;
use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;
use Tigrino\Core\Renderer\RendererInteface;
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
     * @param ContainerInterface $container
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(App &$app, ContainerInterface $container)
    {
        $this->app = &$app;
        $this->app->getRouter()->addRoutes(include __DIR__ . "/Config/Routes.php");

        /** @var RendererInteface $renderer */
        $renderer = $container->get(RendererInteface::class);
        $renderer->addPath(dirname(__DIR__, 2) . '/Templates/Auth', 'Auth');

        $this->addAuthMiddleware($container);
    }

    private function addAuthMiddleware($container): void
    {
        $this->app->addMiddleware(new AuthMiddleware($container));
    }
}
