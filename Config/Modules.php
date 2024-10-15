<?php

/**
 * L'injection des modules ici se fait par nom de dossier.
 */

use Tigrino\App\Ecoride\EcorideModule;
use Tigrino\Auth\AuthModule;
use Tigrino\Errors\ErrorModule;

return [
    EcorideModule::class,
    AuthModule::class,
    ErrorModule::class,
];
