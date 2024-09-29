<?php

namespace Tests\Modules;

use Tigrino\Core\App;
use Tigrino\Core\Modules\ModuleInterface;

class TestModules implements ModuleInterface
{
    private ?string $message = null;

    /**
     * @inheritDoc
     */
    public function __invoke(App &$app): void
    {
        $this->message = "Ce module a été activé";
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }
}
