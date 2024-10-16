<?php

use Tigrino\App\Admin\Controller\AdminController;
use Tigrino\Auth\Config\Role;

return [
    [ 'GET', '/admin', [AdminController::class, 'index'], 'admin', [Role::ADMIN]],
];
