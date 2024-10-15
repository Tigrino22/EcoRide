<?php

namespace Tigrino\Errors;

use Psr\Container\ContainerInterface;
use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;
use Tigrino\Core\Renderer\RendererInteface;

class ErrorModule implements ModuleInterface
{

    private App $app;

    public function __construct(App &$app, ContainerInterface $container)
    {
        $this->app = &$app;
        $this->app->getRouter()->addRoutes(include __DIR__ . "/Config/Routes.php");

        /** @var RendererInteface $renderer */
        $renderer = $container->get(RendererInteface::class);
        $renderer->addPath(dirname(__DIR__, 2) . '/Templates/Errors', 'Errors');
    }
}
