<?php

use Tigrino\App\Covoiturage\Controller\CovoiturageController;

return [
    [ 'GET', '/covoiturage', [CovoiturageController::class, 'index'], 'covoiturage', []],
];
