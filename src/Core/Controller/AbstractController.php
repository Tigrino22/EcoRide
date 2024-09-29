<?php

namespace Tigrino\Core\Controller;

use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbstractController
{
    /**
     * @var RequestInterface|ServerRequestInterface
     */
    protected RequestInterface|ServerRequestInterface $request;

    /**
     * Exécute la méthode demandée avec les paramètres et la requête.
     *
     * @param string $method
     * @param array $params
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws \Exception
     */
    public function execute(string $method, array $params, ServerRequestInterface $request): ResponseInterface
    {

        $this->request = $request;

        if (method_exists($this, $method)) {
            return $this->$method(...$params);
        } else {
            throw new \Exception("Méthode {$method} non trouvée dans le contrôleur.");
        }
    }
}
