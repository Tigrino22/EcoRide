<?php

use Tigrino\Core\Renderer\PHPRenderer;

return [
    PHPRenderer::class => function () {
        return new PHPRenderer();
    }
];
