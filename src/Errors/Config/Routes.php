<?php

use Tigrino\Errors\Controller\ErrorController;

return [
    ["GET",
        "/403",
        [ErrorController::class, "error403"],
        "error.403",
        []],
];