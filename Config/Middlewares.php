<?php

/**
 * Tableau où sont inscris tous les middlewares de l'application
 * Les middlewares sont ici à instancier.
 */

use Tigrino\Core\Middleware\CORSMiddleware;
use Tigrino\Core\Middleware\TrailingSlashMiddleware;

return [
    new TrailingSlashMiddleware(),
    //new CORSMiddleware(),
];
