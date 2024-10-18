<?php

namespace Tigrino\App\Profile;

use Psr\Container\ContainerInterface;
use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;
use Tigrino\Core\Renderer\RendererInterface;

class ProfilModule implements ModuleInterface
{
    private App $app;

    public function __construct(App &$app, ContainerInterface $container)
    {
        $this->app = &$app;
        $this->app->getRouter()->addRoutes(include __DIR__ . '/Config/Routes.php');

        /** @var RendererInterface $renderer */
        $renderer = $container->get(RendererInterface::class);
        $renderer->addPath(dirname(__DIR__, 3) . '/Templates/Profil', 'Profil');
    }
}
