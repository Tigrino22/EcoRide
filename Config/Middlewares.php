<?php

/**
 * Tableau où sont inscris tous les middlewares de l'application
 * Les middlewares sont ici à instancier.
 */

use Tigrino\Auth\Middleware\AuthMiddleware;
use Tigrino\Core\Middleware\CORSMiddleware;
use Tigrino\Core\Middleware\CSRFMiddleware;
use Tigrino\Core\Middleware\TrailingSlashMiddleware;

return [
    TrailingSlashMiddleware::class,
    //CORSMiddleware::class,
    //CSRFMiddleware::class,
    AuthMiddleware::class,
];
