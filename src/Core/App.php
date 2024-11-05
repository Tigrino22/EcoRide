<?php

namespace Tigrino\Core;

use Psr\Container\ContainerInterface;
use Relay\Relay;
use Config\Config;
use Tigrino\Core\Router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Tigrino\Core\Router\RouterInterface;
use Tigrino\Core\Modules\ModuleInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * App
 */
class App
{
    /**
     * Middlewares de l'application
     *
     *  @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * Modules a charger du programme
     *
     *  @var ModuleInterface[]
     */
    private array $modules = [];

    /**
     * @var ContainerInterface
     */
    private ContainerInterface $container;

    /**
     * __construct
     * Prends en paramètre un tableau de modules via un fichier de configuration
     *
     * @param array $modules
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container, array $modules = [])
    {
        $this->container = $container;

        // Ajout des routes générales. CONFIG_DIR = ./Config/**
        $this->container->get(Router::class)->addRoutes(include(Config::CONFIG_DIR . "Routes.php"));

        /**
         * Initialisation de chaque module avec le container
         */
        foreach ($modules as $module) {
            $this->modules[] = new $module($this, $this->container);
        }
    }

    /**
     * Fonction ajoutant des middlewares a l'application
     *
     *  @param string|null
     */
    public function addMiddleware($middlewares = null): void
    {
        if ($middlewares) {
            if (is_array($middlewares)) {
                foreach ($middlewares as $middleware) {
                    if (is_string($middleware)) {
                        $this->middlewares[] = $this->container->get($middleware);
                    } else {
                        throw new \InvalidArgumentException("Expected string class name, got " . gettype($middleware));
                    }
                }
            } else {
                if (is_string($middlewares)) {
                    $this->middlewares[] = $this->container->get($middlewares);
                } else {
                    throw new \InvalidArgumentException("Expected string class name, got " . gettype($middlewares));
                }
            }
        }
    }

    public function getMiddleware(): array
    {
        return $this->middlewares;
    }

    /**
     * run
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function run(ServerRequestInterface $request): ResponseInterface
    {
        // Last middleware pour géré le routing
        $this->middlewares[] = function ($request, $handler) {
            return $this->container->get(Router::class)->dispatch($request);
        };

        // Execution de la pile de middleware.
        $relay = new Relay($this->middlewares);

        return $relay->handle($request);
    }

    /**
     * Getter nécessaire pour que chaque module
     * initialise ses propres routes.
     *
     * @return RouterInterface
     */
    public function getRouter(): RouterInterface
    {
        return $this->container->get(Router::class);
    }

    public function getModules(): array
    {
        return $this->modules;
    }
}
