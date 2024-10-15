<?php

namespace Tigrino\Http\Errors;

use GuzzleHttp\Psr7\Response;

/**
 * Classe gestionnaire de l'affichage de l'erreur 404
 *
 */
class NotFoundResponse extends Response
{
    public function __construct(
        int $status = 404,
        array $headers = [],
        $body = null
    ) {
        parent::__construct($status, $headers, $body);
    }

    public static function create(string $body): Response
    {
        return new self(body: $body);
    }
}
