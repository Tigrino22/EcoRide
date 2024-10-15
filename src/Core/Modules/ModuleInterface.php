<?php

namespace Tigrino\Core\Modules;

use Psr\Container\ContainerInterface;
use Tigrino\Core\App;

interface ModuleInterface
{
    /**
     * L'implémentation de cette fonction dans les modules est
     * nécessaire afin d'initialiser le module en question.
     *
     */
    public function __construct(App &$app, ContainerInterface $container);
}
