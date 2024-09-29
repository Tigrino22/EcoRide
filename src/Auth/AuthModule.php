<?php

namespace Tigrino\Auth;

use Tigrino\Auth\Middleware\AuthMiddleware;
use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;
use Tigrino\Core\Router\RouterInterface;

class AuthModule implements ModuleInterface
{
    /**
     * @var App
     */
    private $app;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * Méthode a implémenter en fonction du fonctionnement
     * du AuthModule.
     *
     * @param App $app
     * @return void
     */
    public function __invoke(App &$app): void
    {
        $this->app = &$app;
        /** @var RouterInterface */
        $this->router = $this->app->getRouter();

        $this->router->addRoutes(include __DIR__ . "/Config/Routes.php");

        $this->addAuthMiddleware();
    }

    private function addAuthMiddleware()
    {
        $protectedRoutes = $this->router->getProtectedRoutes();
        $this->app->addMiddleware(new AuthMiddleware($this->router));
    }
}
