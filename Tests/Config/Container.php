<?php

use Tigrino\Core\Renderer\RendererInterface;
use Tigrino\Core\Renderer\TwigRendererFactory;

use function DI\factory;

return [
    'templates.path' => dirname(__DIR__) . '/Templates',
    'asset.path' => dirname(__DIR__) . '/Public/assets', // Chemin de chargement des assets en prod
    'environnement' => 'DEV',
    RendererInterface::class => factory(TwigRendererFactory::class),
];
