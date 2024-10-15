<?php

namespace Tigrino\Http\Response;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Utils;

class RedirectResponse extends Response
{
    /**
     * Crée une réponse de redirection avec l'URL cible.
     *
     * @param string $path L'URL vers laquelle rediriger
     * @param int $status Le code de statut de la redirection (par défaut 303)
     * @param array $headers Les en-têtes supplémentaires (par défaut vide)
     * @return static
     */
    public static function create(string $path, int $status = 302, array $headers = []): self
    {
        $headers = array_merge($headers, ['Location' => $path]);

        return new self(status: $status, headers: $headers, body: Utils::streamFor(''));
    }
}
