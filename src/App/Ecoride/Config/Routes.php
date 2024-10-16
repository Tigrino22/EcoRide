<?php

use Tigrino\App\Ecoride\Controller\AuthController;
use Tigrino\App\Ecoride\Controller\CovoiturageController;
use Tigrino\App\Ecoride\Controller\HomeController;
use Tigrino\Auth\Config\Role;

return [
    [ "GET",        '/',            [HomeController::class, 'index'],           'home',             []],
    [ "GET",        '/contact',     [HomeController::class, 'contact'],         'home.contact',     []],
    [ "GET",        '/covoiturage', [CovoiturageController::class, 'index'],    'covoiturage',      []],
];
