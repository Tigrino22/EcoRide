<?php

/**
 * L'injection des modules ici se fait par nom de dossier.
 */

use Tigrino\Auth\AuthModule;
use Tigrino\App\Home\HomeModule;
use Tigrino\App\Covoiturage\CovoiturageModule;

return [
    CovoiturageModule::class,
    HomeModule::class,
    AuthModule::class,
];
