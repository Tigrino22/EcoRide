<?php

namespace Tigrino\Http\Errors;

use GuzzleHttp\Psr7\Response;

/**
 * Classe gestionnaire de l'affichage de l'erreur 403
 *
 */
class UnauthorizedMethodResponse extends Response
{
    public function __construct(
        int $status = CodeError::UNAUTHORIZEDMETHOD,
        array $headers = [],
        $body = null
    ) {

        parent::__construct($status, $headers, $body);
    }

    public static function create(string $body): UnauthorizedMethodResponse
    {
        return new self(body: $body);
    }
}
