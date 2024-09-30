<?php

namespace Tigrino\App\Home;

use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;

class HomeModule implements ModuleInterface
{
    private App $app;

    public function __invoke(App &$app): void
    {
        $this->app = &$app;
        $this->app->getRouter()->addRoutes(include __DIR__ . "/Config/Routes.php");
    }
}
