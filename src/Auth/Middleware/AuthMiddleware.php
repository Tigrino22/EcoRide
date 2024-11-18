<?php

namespace Tigrino\Auth\Middleware;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Tigrino\Auth\Entity\GuestUser;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Misc\VarDumper;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Core\Session\SessionManagerInterface;
use Tigrino\Http\Response\RedirectResponse;

class AuthMiddleware implements MiddlewareInterface
{
    private Router $router;
    private UserRepository $userRepository;
    private SessionManagerInterface $sessionManager;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container, UserRepository $userRepository = null)
    {
        $this->container = $container;
        $this->router = $this->container->get(Router::class);
        $this->userRepository = $userRepository ?? new UserRepository();
        $this->sessionManager = $this->container->get(SessionManager::class);
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
        $protectedRoutes = $this->router->getProtectedRoutes();

        // Match pour trouver une route qui correspond a la requête
        $match = $this->router->match($method, $path);

        // Si une route est trouvée
        if ($match) {
            // Vérifier si cette route est protégée
            foreach ($protectedRoutes as $protectedRoute) {
                if ($match['name'] === $protectedRoute['name']) {
                    $requiredRoles = $protectedRoute['role'];

                    /**
                     * Recherche de l'utilisateur par l'id dans la session
                     */
                    if ($this->sessionManager->has('user')) {
                        $id = $this->sessionManager->get('user')['id'];

                        try {
                            /** @var User $user */
                            $user = $this->userRepository->findById($id);
                        } catch (\Exception $e) {
                            echo "L'utilisateur n'a pas pu être retrouver : $id | Message : " . $e->getMessage();
                        }
                    } else {
                        $user = new GuestUser();
                    }

                    // Vérifier si l'utilisateur a les rôles requis
                    if (count($requiredRoles) > 0 && !$this->hasRole($user, $requiredRoles)) {
                        try {
                            return RedirectResponse::create(
                                $this->router->generate('error.403')
                            );
                        } catch (\Exception $e) {
                            echo sprintf(
                                "Erreur lors de la redirect vers le page 403 dans le authMiddleware: %s",
                                $e->getMessage()
                            );
                        }
                    }
                }
            }
        }

        return $handler->handle($request);
    }

    private function hasRole(User $user, array $requiredRoles): bool
    {
        $userRoles = $this->userRepository->getRoles($user);

        if (!empty(array_intersect($userRoles, $requiredRoles))) {
            return true;
        }

        return false;
    }
}
