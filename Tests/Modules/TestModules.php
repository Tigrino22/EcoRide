<?php

namespace Tests\Modules;

use Psr\Container\ContainerInterface;
use Tigrino\Core\Modules\ModuleInterface;

class TestModules implements ModuleInterface
{
    private ?string $message = null;

    /**
     * @inheritDoc
     */
    public function __construct(ContainerInterface $container)
    {
        $this->message = "Ce module a été activé";
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
