<?php

namespace Tigrino\Core\Router;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

interface RouterInterface
{
    /**
     * Méthode pour ajouter les routes au router franchaiment initialisé.
     *
     * @param array $routes
     * @return void
     */
    public function addRoutes(array $routes): void;

    /**
     * Retrieves all routes.
     * Useful if you want to process or display routes.
     * @return array All routes.
     */
    public function getRoutes(): array;

        /**
     * Retrieves all protected routes by Auth.
     * Useful if you want to process or display protected routes.
     * @return array protected routes.
     */
    public function getProtectedRoutes(): array;

    /**
     * Match a given Request Url against stored routes
     * @param string $requestUrl
     * @param string $requestMethod
     * @return ?array Array with route information on success, false on failure (no match).
     */
    public function match(string $requestMethod, string $requestUri): ?array;

    /**
     * Après le match, méthode qui va chercher le callback afin de l'appeler.
     *
     * @param RequestInterface
     * @return ResponseInterface
     */
    public function dispatch(RequestInterface $request): ResponseInterface;

    /**
     * Generate the URL for a named route. Replace regexes with supplied parameters
     *
     * @param string $routeName The name of the route.
     * @param array @params Associative array of parameters to replace placeholders with.
     * @return string The URL of the route with named parameters in place.
     */
    public function generate($routeName, array $params = []): string;
}
