<?php

use Tigrino\Core\Renderer\RendererInteface;
use Tigrino\Core\Renderer\TwigRendererFactory;
use Tigrino\Core\Router\Router;

use function DI\autowire;
use function DI\env;
use function DI\factory;

return [
    'templates.path' => dirname(__DIR__) . '/Templates', // Pour le namespace __main__ twig
    'asset.path' => dirname(__DIR__) . '/Public/assets', // Chemin de chargement des assets en prod
    'environnement' => env('APP_ENV', 'DEV'), // Sert notamment pour le front avec Vite et TwigAssetExtension
    'modules' => include(__DIR__ . '/Modules.php'),

    RendererInteface::class => factory(TwigRendererFactory::class),
    Router::class => autowire(Router::class),
];
