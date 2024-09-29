<?php

namespace Tigrino\Auth\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Tigrino\Auth\Entity\User;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Http\Response\JsonResponse;
use Tigrino\Auth\Repository\UserRepository;

class AuthController extends AbstractController
{
    private UserRepository $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function register(): ResponseInterface
    {
        if ($this->request->getMethod() === "POST") {
            $data = $this->request->getParsedBody();

            $username = $data['username'] ?? null;
            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;
            $role = $data['role'] ?? 'user';

            if (!$username || !$email || !$password) {
                return new JsonResponse(
                    status: 400,
                    data: ['message' => 'Champs manquants']
                );
            }

            $hasedPassword = password_hash($password, PASSWORD_BCRYPT);

            $user = new User(
                id: Uuid::uuid4(),
                username: $username,
                password: $hasedPassword,
                roles: $role,
                email: $email,
                lastLogin: date('Y-m-d H:i:s')
            );

            $this->userRepository->insert($user);

            return new JsonResponse(
                status: 201,
                data: [
                    'message' => 'Utilisateur créé avec succès',
                    'Utilisateur uuid : ' => $user->getUuid()
                ]
            );
        }

        return new Response(
            status: 200,
            body: "<h1>Page d'inscription</h1>"
        );
    }

    public function login(): ResponseInterface
    {
        if ($this->request->getMethod() === "POST") {
            $data = $this->request->getParsedBody();

            $email = $data['email'] ?? null;
            $password = $data['password'] ?? null;

            if (!$email || !$password) {
                return new JsonResponse(
                    status: 400,
                    data: ['message' => 'Email ou mot de passe manquant']
                );
            }

            /** @var User $user */
            $user = $this->userRepository->findByEmail($email);
            if (!$user || !password_verify($password, $user->getPassword())) {
                return new JsonResponse(
                    status: 401,
                    data: ['message' => 'Identifiants invalides']
                );
            }

            $sessionToken = bin2hex(random_bytes(16));
            $user->setSessionToken($sessionToken);
            $user->setUserCookie($user->getSessionToken());
            $user->setLastLogin(date('Y-m-d H:i:s'));

            $this->userRepository->update($user);

            // TODO implémentation de la connexion via cookies pour API
            return new JsonResponse(
                status: 200,
                data: [
                    'message' => 'Connexion réussie',
                    'session_token' => $sessionToken
                ]
            );
        }

        return new Response(
            status: 200,
            body: "<h1>Page de connexion</h1>"
        );
    }

    public function logout(): ResponseInterface
    {
        if ($this->request->getMethod() === "POST") {
            $sessionToken = $this->request->getCookieParams()['session_token'];

            if ($sessionToken) {
                $user = $this->userRepository->findBySessionToken($sessionToken);

                if ($user) {
                    $user->setSessionToken(null);
                    $this->userRepository->update($user);

                    // Suppression du cookie
                    setcookie("session_token", "", time() - 3600, "/");

                    return new JsonResponse(
                        status: 200,
                        data: ['message' => 'Déconnexion réussie']
                    );
                } else {
                    return new JsonResponse(
                        status: 404,
                        data: ['message' => 'Aucun utilisateur trouvé avec ce Token']
                    );
                }
            } else {
                return new JsonResponse(
                    status: 404,
                    data: ['message' => 'Aucun Token provide']
                );
            }
        }

        return new Response(
            status: 200,
            body: "<h1>Page de déconnexion</h1>"
        );
    }
}
