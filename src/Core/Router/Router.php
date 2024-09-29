<?php

namespace Tigrino\Core\Router;

use AltoRouter;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Core\Router\Exception\ControllerException;

/**
 * Router based on Altorouter
 * https://packagist.org/packages/altorouter/altorouter
 *
 * POur ajouter des routes:
 * Dans le ./Config/Routes.php
 *
 * ex:
 *  return [
 *          [ "GET", "/blog", [ExampleController::class, "index"], "blog.show" ],
 *          [ "GET", "/blog/show-[i:id]", [ExampleController::class, "show"], "blog.show" ],
 *          ["GET", "/blog/name-[a:name]", [ExampleController::class, "admin"], "blog.admin", ["admin"]],
 *      ];
 *
 * Pour les paramètrs, voir le lien suivant :
 * https://dannyvankooten.github.io/AltoRouter/usage/mapping-routes.html
 *
 */
class Router implements RouterInterface
{
    /**
     * router
     *
     * @var Altorouter
     */
    private $router = null;

    /**
     * Tableaux representant les routes protégées
     * par un parfeu.
     *
     * @var string[]
     */
    private $protectedRoutes = [];

    /**
     * Initialise l'instance d'AltoRouter lors de la création du Router.
     */
    public function __construct()
    {
        $this->router = new AltoRouter();
    }

    /**
     * Méthode pour ajouter les routes au router franchaiment initialisé.
     *
     * @param array $routes
     * @return void
     */
    public function addRoutes(array $routes): void
    {
        foreach ($routes as $route) {
            [$method, $path, $callback, $name, $role] = $route;
            $this->router->map($method, $path, $callback, $name);
            if (!empty($role)) {
                $this->protectedRoutes[$path] = ["name" => $name, "role" => $role]; // Utiliser le chemin comme clé
            }
        }
    }

    /**
     * Retrieves all routes.
     * Useful if you want to process or display routes.
     * @return array All routes.
     */
    public function getRoutes(): array
    {
        return $this->router->getRoutes();
    }

    /**
     * Retrieves all protected routes by Auth.
     * Useful if you want to process or display protected routes.
     * @return array protected routes.
     */
    public function getProtectedRoutes(): array
    {
        return $this->protectedRoutes;
    }

    /**
     * Match a given Request Url against stored routes
     * @param string $requestMethod
     * @param string $requestUri
     * @return ?array Array with route information on success, false on failure (no match).
     */
    public function match(string $requestMethod, string $requestUri): ?array
    {
        $match = $this->router->match($requestUri, $requestMethod);

        return $match ?: null;
    }

    /**
     * Après le match, méthode qui va chercher le callback afin de l'appeler.
     *
     * @param RequestInterface $request
     * @return ResponseInterface
     * @throws ControllerException
     * @throws \Exception
     */
    public function dispatch(RequestInterface $request): ResponseInterface
    {
        $route = $this->match($request->getMethod(), (string) $request->getUri()->getPath());

        if ($route) {
            if (is_array($route['target'])) {
                [$controller, $method] = $route['target'];
                $params = $route['params'];

                if (class_exists($controller) && is_subclass_of($controller, AbstractController::class)) {
                    $controllerInstance = new $controller();
                    return $controllerInstance->execute($method, $params, $request);
                } else {
                    throw new ControllerException(
                        "Le controller {$controller} n'est pas trouvable 
                        ou n'est pas un sous-classe d'AbstractController."
                    );
                }
            } elseif (is_callable($route['target'])) {
                return call_user_func($route['target']);
            } else {
                throw new ControllerException("La target {$route['target']} n'est pas une callable.");
            }
        } else {
            return new Response(404, [], "<h1>Page not found</h1>");
        }
    }


    /**
     * Generate the URL for a named route. Replace regexes with supplied parameters
     *
     * @param string $routeName The name of the route.
     * @param array $params Associative array of parameters to replace placeholders with.
     * @return string The URL of the route with named parameters in place.
     * @throws \Exception
     */
    public function generate($routeName, array $params = []): string
    {
        return $this->router->generate($routeName, $params);
    }
}
