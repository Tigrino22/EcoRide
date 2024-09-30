<?php

use Tigrino\Core\Renderer\RendererInteface;
use Tigrino\Core\Renderer\TwigRendererFactory;
use Tigrino\Core\Router\Router;

use function DI\autowire;
use function DI\factory;

return [
    'templates.path' => dirname(__DIR__) . '/Templates',
    'modules' => include(__DIR__ . '/Modules.php'),

    RendererInteface::class => factory(TwigRendererFactory::class),
    Router::class => autowire(Router::class),
];
