<?php

namespace Tigrino\App\Profile\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tigrino\App\Profile\Entity\UserEcoride;
use Tigrino\App\Profile\Repository\UserEcorideRepository;
use Tigrino\Auth\Controller\AuthController as AuthControllerFramwork;
use Tigrino\Http\Response\JsonResponse;
use Tigrino\Http\Response\RedirectResponse;

/**
 * Classe d'authentification du projet EcoRide
 * Elle étant directement du AuthControleur du framawork
 *
 */
class AuthController extends AuthControllerFramwork
{
    private UserEcorideRepository $repository;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $this->container->get(UserEcorideRepository::class);
    }

    public function register(): ResponseInterface
    {
        if ($this->request->getMethod() == 'POST') {
            $data = $this->request->getParsedBody();
            if ($data['password'] != $data['confirm_password']) {
                return new JsonResponse(
                    status: 400,
                    data: ['message' => 'Les mot de passe ne correspondent pas']
                );
            }

            if (
                !$data['username'] ||
                !$data['email'] ||
                !$data['password'] ||
                !$data['name'] ||
                !$data['firstname']
            ) {
                // TODO Créer un flash pour informer de l'erreur
                return RedirectResponse::create('/register');
            }

            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            $user = new UserEcoride($data);

            if (!$this->repository->insert($user)) {
                return new JsonResponse(
                    status: 500,
                    data: ['message' => 'Erreur serveur insertion user']
                );
                // TODO Voir pour Flash égalenement et la gestion des erreurs
            } else {
                $content = $this->render("@Auth/login", ['user' => $user]);

                return new Response(
                    302,
                    [],
                    $content
                );
            }
        }

        return parent::register();
    }

    public function login(): ResponseInterface
    {
        if ($this->request->getMethod() === "POST") {
            $data = $this->request->getParsedBody();

            if (!$data['username'] || !$data['password']) {
                return JsonResponse::create(data: ['message' => 'Identifiants manquant']);
            }

            $user = $this->repository->findByUsername($data['username']);
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
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'firstname' => $user->getFirstname(),
            ]);

            return new Response(
                status: 200,
                headers: ['Location' => '/'],
                body: $this->render("@Home/home")
            );
        }

        return parent::login();
    }
}
