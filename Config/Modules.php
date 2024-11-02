<?php

/**
 * L'injection des modules ici se fait par nom de dossier.
 */

use Tigrino\App\Ecoride\EcorideModule;
use Tigrino\Auth\AuthModule;
use Tigrino\Errors\ErrorModule;
use Tigrino\App\Profile\ProfileModule;
use Tigrino\App\Admin\AdminModule;

return [
    AdminModule::class,
    ProfileModule::class,
    EcorideModule::class,
    AuthModule::class,
    ErrorModule::class,
];
