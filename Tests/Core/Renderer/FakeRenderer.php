<?php

namespace Tests\Core\Renderer;

use Tigrino\Core\Renderer\RendererInterface;

class FakeRenderer implements RendererInterface
{
    public function addPath(string $path, ?string $namespace = null): void
    {
        // TODO: Implement addPath() method.
    }

    public function render(string $view, array $params = []): string
    {
        return 'render';
    }

    public function addGlobals(string $key, $value): void
    {
        // TODO: Implement addGlobals() method.
    }
}
