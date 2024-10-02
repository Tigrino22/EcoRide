<?php

namespace Tigrino\Core\Renderer;

use DI\Container;
use DI\DependencyException;
use DI\NotFoundException;

class TwigRendererFactory
{
    /**
     * @throws NotFoundException
     * @throws DependencyException
     */
    public function __invoke(Container $container): TwigRenderer
    {
        return new TwigRenderer(
            $container->get('templates.path'),
            $container->get('asset.path'),
            $container->get('environnement')
        );
    }
}
