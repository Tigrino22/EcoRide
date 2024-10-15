<?php

namespace Tigrino\Auth\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Tigrino\App\Ecoride\Entity\UserEcoride;
use Tigrino\App\Ecoride\Repository\UserEcorideRepository;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Http\Response\JsonResponse;

class AuthController extends AbstractController
{
    private UserEcorideRepository $userRepository;
    private SessionManager $sessionManager;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->userRepository = new UserEcorideRepository();
        $this->sessionManager = new SessionManager();
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

            $user = new UserEcoride($data);

            if (!$this->userRepository->insert($user)) {
                return new JsonResponse(
                    status: 500,
                    data: ['message' => 'Erreur lors de la création de l\'utilisateur']
                );
            } else {
                // rediriger vers la page Login avec le champ email
                // déja remplie


                $content = $this->render("@Auth/login", ['user' => $user]);

                $response = new Response(body: $content);
                $response->withStatus(303);
                $response->withHeader('Location', '/login');

                return $response;
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

            if (!$data['email'] || !$data['password']) {
                return JsonResponse::create(data: ['message' => 'Identifiants manquant']);
            }

            $user = $this->userRepository->findByEmail($data['email']);
            if (!$user) {
                return JsonResponse::create(
                    data: ['message' => 'Aucun utilisateur trouvé pour cet email']
                );
            }

            if (!password_verify($data['password'], $user->getPassword())) {
                return JsonResponse::create(
                    data: ['message' => 'Mot de passe incorrect']
                );
            }

            $this->sessionManager->set('user', [
                'id' => $user->getUuid(),
                'username' => $user->getUsername(),
                'email' => $user->getEmail(),
                'name' => $user->getName(),
                'firstname' => $user->getFirstname(),
                'telephone' => $user->getTelephone(),
                'address' => $user->getAddress(),
                'birthday' => $user->getBirthday(),
                'photo' => $user->getPhoto(),
            ]);

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

        return new Response(
            status: 200,
            headers: ['Location' => '/'],
            body: $this->render('@Home/home')
        );
    }
}
