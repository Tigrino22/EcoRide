<?php

namespace Tigrino\App\Home;

use Psr\Container\ContainerInterface;
use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;
use Tigrino\Core\Renderer\RendererInteface;

class HomeModule implements ModuleInterface
{
    private App $app;

    public function __construct(ContainerInterface $container)
    {
        $this->app = $container->get(App::class);
        $this->app->getRouter()->addRoutes(include __DIR__ . "/Config/Routes.php");

        /** @var RendererInteface $renderer */
        $renderer = $container->get(RendererInteface::class);
        $renderer->addPath(dirname(__DIR__, 3) . '/Templates/Home', 'Home');
    }
}
