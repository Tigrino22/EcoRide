<?php

namespace Tigrino\Core;

use Relay\Relay;
use Config\Config;
use Tigrino\Core\Router\Router;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\MiddlewareInterface;
use Tigrino\Core\Router\RouterInterface;
use Tigrino\Core\Modules\ModuleInterface;
use Tigrino\Auth\Middleware\AuthMiddleware;
use Psr\Http\Message\ServerRequestInterface;

/**
 * App
 */
class App
{
    /**
     * routerFactory
     *
     * @var Router
     */
    private $router;

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
     * __construct
     * Prends en paramètre un tableau de modules via un fichier de configuration
     *
     * @param array $modules
     *
     * @return void
     */
    public function __construct(array $modules = [])
    {
        $this->router = new Router();

        // Ajout des routes générales. CONFIG_DIR = ./Config/**
        $this->router->addRoutes(include(Config::CONFIG_DIR . "Routes.php"));

        /**
         * Initialisation de chaque module
         * en passant par la méthode __invocke?
         */
        foreach ($modules as $module) {
            $this->modules[] = new $module();
            end($this->modules)($this);
        }
    }

    /**
     * Fonction ajoutant des middlewares a l'application
     *
     *  @param MiddlewareInterface[]|MiddlewareInterface|null
     */
    public function addMiddleware($middlewares = null): void
    {
        if ($middlewares) {
            if (is_array($middlewares)) {
                foreach ($middlewares as $middleware) {
                    $this->middlewares[] = $middleware;
                }
            } else {
                $this->middlewares[] = $middlewares;
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
        $this->addMiddleware(function ($request, $handler) {
            return $this->router->dispatch($request);
        });

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
        return $this->router;
    }

    public function getModules(): array
    {
        return $this->modules;
    }
}
