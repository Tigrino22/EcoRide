<?php

use Tigrino\Errors\Controller\ErrorController;

return [
    ["GET",
        "/forbidden",
        [ErrorController::class, "error403"],
        "error.403",
        []
    ],
    ["GET",
        "/notfound",
        [ErrorController::class, "error404"],
        "error.404",
        []
    ],
    ["GET",
        "/unauthorized",
        [ErrorController::class, "error405"],
        "error.405",
        []
    ],
];
