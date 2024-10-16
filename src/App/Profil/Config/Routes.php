<?php

use Tigrino\App\Profil\Controller\ProfilController;

return [
    [ 'GET', '/profil', [ProfilController::class, 'index'], 'profil', []],
];


