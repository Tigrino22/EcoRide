<?php

namespace Tigrino\Auth\Middleware;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tigrino\Auth\Entity\GuestUser;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Router\RouterInterface;

class AuthMiddleware implements MiddlewareInterface
{
    private $protectedRoutes = [];
    private $router;
    private UserRepository $userRepository;
    private User $user;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
        $this->userRepository = new UserRepository();
    }

    /**
     * Vérification si une route fait partie des routes protéger par un rôle.
     * Si c'est le cas, recupération du token de session de l'utilisateur.
     * Vérification via ce token de session en BDD du role.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {

        // Obtenir le chemin et la méthode HTTP
        $path = $request->getUri()->getPath();
        $method = $request->getMethod();

        // obtention des routes protégées
        $this->protectedRoutes = $this->router->getProtectedRoutes();

        // Match pour trouver une route qui correspond a la requête
        $match = $this->router->match($method, $path);

        // Si une route est trouvée
        if ($match) {
            // Vérifier si cette route est protégée
            foreach ($this->protectedRoutes as $protectedRoute) {
                if ($match['name'] === $protectedRoute['name']) {
                    $requiredRoles = $protectedRoute['role'];

                    /**
                    * Si nous avons un session_token c'est que l'utilisateur est authentifié.
                    */
                    if (isset($request->getCookieParams()['session_token'])) {
                        $user = $this->userRepository->findBySessionToken($request->getCookieParams()['session_token']);
                    } else {
                        $user = new GuestUser();
                    }

                    // Vérifier si l'utilisateur a les rôles requis
                    if (count($requiredRoles) > 0 && !array_intersect($user->getRoles(), $requiredRoles)) {
                        return new Response(403, [], "<h1>Accès interdit</h1>");
                    }
                }
            }
        }

        return $handler->handle($request);
    }
}
