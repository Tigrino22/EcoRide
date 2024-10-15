<?php

namespace Tigrino\Http\Errors;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Tigrino\Core\Renderer\RendererInteface;
use Tigrino\Core\Renderer\TwigRenderer;

/**
 * Classe gestionnaire de l'affichage de l'erreur 403
 *
 */
class ForbiddenResponse extends Response
{
    public function __construct(
        int $status = 403,
        array $headers = [],
        $body = null
    ) {

        parent::__construct($status, $headers, $body);
    }

    public static function create(string $body): ForbiddenResponse
    {
        return new self(body: $body);
    }
}
