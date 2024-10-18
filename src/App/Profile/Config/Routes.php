<?php

use Tigrino\App\Profile\Controller\ProfileController;

return [
    [ 'GET', '/profile', [ProfileController::class, 'index'], 'profile', []],
];
