<?php

use Tigrino\App\Home\Controller\HomeController;

return [
    [ "GET", '/', [HomeController::class, 'index'], 'Home', []],
];
