<?php

use Tigrino\Auth\Controller\AuthController;

return [
    ["GET|POST","/register",    [AuthController::class, "register"],    "auth.register",    []],
    ["GET|POST","/login",       [AuthController::class, "login"],       "auth.login",       []],
    ["POST",    "/logout",      [AuthController::class, "logout"],      "auth.logout",      []],
];
