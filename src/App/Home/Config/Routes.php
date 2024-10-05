<?php

use Tigrino\App\Home\Controller\HomeController;

return [
    [ "GET", '/',           [HomeController::class, 'index'], 'home', []],
    [ "GET", '/contact',    [HomeController::class, 'contact'], 'home.contact', []],
];
