<?php

namespace Tigrino\Auth;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;
use Tigrino\Core\Renderer\RendererInterface;

class AuthModule implements ModuleInterface
{
    /**
     * @var App
     */
    private $app;

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

        /** @var RendererInterface $renderer */
        $renderer = $container->get(RendererInterface::class);
        $renderer->addPath(dirname(__DIR__, 2) . '/Templates/Auth', 'Auth');
    }
}
