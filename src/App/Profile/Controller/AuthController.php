<?php

namespace Tigrino\App\Profile\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tigrino\App\Profile\Entity\UserEcoride;
use Tigrino\App\Profile\Repository\UserEcorideRepository;
use Tigrino\Auth\Controller\AuthController as AuthControllerFramwork;
use Tigrino\Core\Misc\VarDumper;
use Tigrino\Core\Router\Router;
use Tigrino\Http\Response\JsonResponse;
use Tigrino\Http\Response\RedirectResponse;
use Tigrino\Services\FlashService;
use Tigrino\Services\PasswordService;

/**
 * Classe d'authentification du projet EcoRide
 * Elle étant directement du AuthControleur du framawork
 *
 */
class AuthController extends AuthControllerFramwork
{
    private UserEcorideRepository $repository;
    private FlashService $flashService;
    private Router $router;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->repository = $container->get(UserEcorideRepository::class);
        $this->flashService = $container->get(FlashService::class);
        $this->router = $container->get(Router::class);
    }

    public function register(): ResponseInterface
    {
        if ($this->request->getMethod() == 'POST') {
            $data = $this->request->getParsedBody();

            if (
                !$data['username'] ||
                !$data['email'] ||
                !$data['password'] ||
                !$data['name'] ||
                !$data['firstname']
            ) {
                $this->flashService->add('error', 'Merci de compléter tous les champs');
                return RedirectResponse::create(
                    $this->router->generate('auth.register')
                );
            }

            // Vérification de la sécurité du mot de passe
            $passwordCheck = PasswordService::passwordValidator($data['password']);
            if (is_array($passwordCheck)) {
                foreach ($passwordCheck as $error) {
                    $this->flashService->add('error', $error);
                }
                return RedirectResponse::create(
                    path: $this->router->generate('auth.register'),
                    status: 400
                );
            }

            if ($data['password'] != $data['confirm_password']) {
                $this->flashService->add('error', 'Les mots de passe ne correspondent pas.');
                return RedirectResponse::create(
                    path: $this->router->generate('auth.register'),
                    status: 400
                );
            }



            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

            $user = new UserEcoride($data);

            if (!$this->repository->insert($user)) {
                $this->flashService->add('error', 'Une erreur est survenue lors de la création de votre compte.
                \nMerci de réessayer utltérieurement.
                \nSi le problème persiste, veulliez contacter le support technique.');

                return RedirectResponse::create(
                    $this->router->generate('auth-register')
                );
            } else {
                $this->flashService->add('success', 'Votre compte a été créé avec succes!');
                return new Response(
                    302,
                    [],
                    $this->render("@Auth/login", ['user' => $user])
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
