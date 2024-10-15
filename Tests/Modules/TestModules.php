<?php

namespace Tests\Modules;

use Psr\Container\ContainerInterface;
use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;

class TestModules implements ModuleInterface
{
    private ?string $message = null;
    private App $app;

    /**
     * @inheritDoc
     */
    public function __construct(App &$app, ContainerInterface $container)
    {
        $this->app = &$app;
        $this->message = "Ce module a été activé";
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
