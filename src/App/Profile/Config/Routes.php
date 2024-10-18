<?php

use Tigrino\App\Profile\Controller\AuthController;
use Tigrino\App\Profile\Controller\ProfileController;

return [
    [ 'GET', '/profile', [ProfileController::class, 'index'], 'profile', []],

    // AUTHENTIFICATION
    ["GET|POST",
        "/register",
        [AuthController::class, "register"],
        "auth.register",
        []],
    ["GET|POST",
        "/login",
        [AuthController::class, "login"],
        "auth.login",
        []],
    ["GET",
        "/logout",
        [AuthController::class, "logout"],
        "auth.logout",
        []],
];
