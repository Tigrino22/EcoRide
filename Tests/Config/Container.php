<?php

use Tigrino\Core\Renderer\RendererInteface;
use Tigrino\Core\Renderer\TwigRendererFactory;

use function DI\factory;

return [
    'templates.path' => dirname(__DIR__) . '/Templates',
    RendererInteface::class => factory(TwigRendererFactory::class),
];
