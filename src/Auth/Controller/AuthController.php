<?php

namespace Tigrino\Auth\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Http\Response\JsonResponse;
use Tigrino\Http\Response\RedirectResponse;
use Tigrino\Services\CookieManager;

class AuthController extends AbstractController
{
    protected UserRepository $userRepository;
    protected SessionManager $sessionManager;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->sessionManager = $container->get(SessionManager::class);
        $this->userRepository = $container->get(UserRepository::class);
    }

    public function register(): ResponseInterface
    {
        if ($this->request->getMethod() === "POST") {
            $data = $this->request->getParsedBody();

            if ($data['password'] != $data['confirm_password']) {
                return new JsonResponse(
                    status: 400,
                    data: ['message' => 'Les mot de passe ne correspondent pas']
                );
            }

            $user = new User(
                username: $data['username'],
                password: password_hash($data['password'], PASSWORD_DEFAULT),
            );

            if (!$this->userRepository->insert($user)) {
                return new JsonResponse(
                    status: 500,
                    data: ['message' => 'Erreur lors de la création de l\'utilisateur']
                );
            } else {
                // rediriger vers la page Login avec le champ email

                $content = $this->render("@Auth/login", ['user' => $user]);

                $response = new Response(body: $content);
                return $response->withStatus(303)->withHeader('Location', '/login');
            }
        }

        // Methode GET
        $content = $this->render('@Auth/register');

        return new Response(
            200,
            [],
            $content
        );
    }

    public function login(): ResponseInterface
    {
        if ($this->request->getMethod() === "POST") {
            $data = $this->request->getParsedBody();

            if (!$data['username'] || !$data['password']) {
                return JsonResponse::create(data: ['message' => 'Identifiants manquant']);
            }

            $user = $this->userRepository->findByUsername($data['username']);
            if (!$user) {
                return JsonResponse::create(
                    data: ['message' => 'Aucun utilisateur trouvé pour cet username']
                );
            }

            if (!password_verify($data['password'], $user->getPassword())) {
                return JsonResponse::create(
                    data: ['message' => 'Mot de passe incorrect']
                );
            }

            $this->sessionManager->set('user', [
                'id' => $user->getId(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail()
            ]);
            CookieManager::set('user_id', $user->getId(), 3600 * 24 * 7 * 360, false, false);

            return new Response(
                status: 200,
                headers: ['Location' => '/'],
                body: $this->render("@Home/home")
            );
        }

        // Methode GET
        $content = $this->render('@Auth/login');

        return new Response(
            200,
            [],
            $content
        );
    }

    public function logout(): ResponseInterface
    {
        $this->sessionManager->remove('user');
        CookieManager::delete('user_id');

        return RedirectResponse::create(
            $this->container->get(Router::class)->generate('home')
        );
    }
}
