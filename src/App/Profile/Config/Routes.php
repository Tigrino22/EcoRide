<?php

use Tigrino\App\Profile\Controller\AuthController;
use Tigrino\App\Profile\Controller\CarController;
use Tigrino\App\Profile\Controller\ProfileController;
use Tigrino\Auth\Config\Role;

return [
    [ 'GET',
        '/profile',
        [ProfileController::class, 'index'],
        'profile',
        [Role::USER, Role::ADMIN]],

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
