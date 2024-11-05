<?php


use Tigrino\App\Profile\Repository\UserEcorideRepository;
use Tigrino\Auth\Controller\AuthController;
use Tigrino\Core\Renderer\Extensions\TwigFlashExtension;
use Tigrino\Core\Renderer\RendererInterface;
use Tigrino\Core\Renderer\TwigRendererFactory;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Core\Session\SessionManagerInterface;
use Tigrino\Services\FlashService;
use Tigrino\Services\SerializerService;
use function DI\autowire;
use function DI\create;
use function DI\env;
use function DI\factory;
use function DI\get;

return [
    'templates.path' => dirname(__DIR__) . '/Templates', // Pour le namespace __main__ twig
    'asset.path' => dirname(__DIR__) . '/Public/assets', // Chemin de chargement des assets en prod
    'environnement' => env('APP_ENV', 'DEV'), // Sert notamment pour le front avec Vite et TwigAssetExtension
    'modules' => include(__DIR__ . '/Modules.php'),

    RendererInterface::class => factory(TwigRendererFactory::class),
    SessionManagerInterface::class => autowire(SessionManager::class),
    FlashService::class => autowire(FlashService::class)
        ->constructor(get(SessionManager::class)),
    TwigFlashExtension::class => autowire(TwigFlashExtension::class)
        ->constructor(get(FlashService::class)),
];
