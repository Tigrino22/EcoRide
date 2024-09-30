<?php

namespace Tigrino\Core\Renderer;

use DI\Container;

class TwigRendererFactory
{
    public function __invoke(Container $container): TwigRenderer
    {
        return new TwigRenderer($container->get('templates.path'));
    }
}
