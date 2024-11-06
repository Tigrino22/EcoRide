<?php

use Tigrino\App\Profile\Controller\AuthController;
use Tigrino\App\Profile\Controller\CarController;
use Tigrino\App\Profile\Controller\ProfileController;
use Tigrino\Auth\Config\Role;

return [
    // PROFIL
    [ 'GET',
        '/profile',
        [ProfileController::class, 'index'],
        'profile',
        [Role::USER, Role::ADMIN]],

    [ 'POST',
        '/profile/update/[*:id]',
        [ProfileController::class, 'update'],
        'profile.update',
        [Role::USER, Role::ADMIN]],

    [ 'POST',
        '/profile/updateDriver/[*:id]',
        [ProfileController::class, 'updateDriver'],
        'profile.update.driver',
        [Role::USER, Role::ADMIN]],

    // VOITURE
    [ 'GET',
        '/car',
        [CarController::class, 'show'],
        'car.show',
        [Role::USER, Role::ADMIN]],

    [ 'GET|POST',
        '/car/insert',
        [CarController::class, 'insert'],
        'car.insert',
        [Role::USER, Role::ADMIN]],

    [ 'GET|POST',
        '/car/update/[*:id]',
        [CarController::class, 'update'],
        'car.update',
        [Role::USER, Role::ADMIN]],

    [ 'POST',
        '/car/delete/[*:id]',
        [CarController::class, 'delete'],
        'car.delete',
        [Role::USER, Role::ADMIN]],

    // AUTHENTIFICATION
    ["GET|POST",
        "/register",
        [AuthController::class, "register"],
        "auth.register",
        [Role::GUEST]],
    ["GET|POST",
        "/login",
        [AuthController::class, "login"],
        "auth.login",
        [Role::GUEST]],
    ["GET",
        "/logout",
        [AuthController::class, "logout"],
        "auth.logout",
        [Role::USER, Role::ADMIN]],
];
