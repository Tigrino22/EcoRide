<?php

use Tigrino\Auth\Controller\AuthController;

return [
    ["GET",     "/register",    [AuthController::class, "register"],    "auth.register.get",    []],
    ["POST",    "/register",    [AuthController::class, "register"],    "auth.register.post",   []],
    ["GET",     "/login",       [AuthController::class, "login"],       "auth.login.get",       []],
    ["POST",    "/login",       [AuthController::class, "login"],       "auth.login.post",      []],
    ["GET",     "/logout",      [AuthController::class, "logout"],      "auth.logout.get",      []],
    ["POST",    "/logout",      [AuthController::class, "logout"],      "auth.logout.post",     []],
];
