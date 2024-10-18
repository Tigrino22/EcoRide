<?php

use Psr\Container\ContainerInterface;

use function DI\env;
use function DI\factory;
use function DI\autowire;

use Tigrino\Core\Router\Router;
use Tigrino\Auth\Controller\AuthController;
use Tigrino\Core\Renderer\RendererInterface;
use Tigrino\Core\Renderer\TwigRendererFactory;

return [
    'templates.path' => dirname(__DIR__) . '/Templates', // Pour le namespace __main__ twig
    'asset.path' => dirname(__DIR__) . '/Public/assets', // Chemin de chargement des assets en prod
    'environnement' => env('APP_ENV', 'DEV'), // Sert notamment pour le front avec Vite et TwigAssetExtension
    'modules' => include(__DIR__ . '/Modules.php'),

    RendererInterface::class => factory(TwigRendererFactory::class),
    Router::class => autowire(Router::class),
    AuthController::class => autowire(AuthController::class)
];
