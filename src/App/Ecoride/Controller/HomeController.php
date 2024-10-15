<?php

namespace Tigrino\App\Ecoride\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Tigrino\App\Ecoride\Entity\UserEcoride;
use Tigrino\App\Ecoride\Repository\UserEcorideRepository;
use Tigrino\Auth\Config\Role;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Http\Response\JsonResponse;

class HomeController extends AbstractController
{
    public function index(): ResponseInterface
    {
        $content = $this->render('@Home/home');

        return new Response(
            200,
            [],
            $content
        );
    }

    public function contact(): ResponseInterface
    {
        $content = $this->render('@Home/contact');

        return new Response(
            200,
            [],
            $content
        );
    }

    public function admin(): ResponseInterface
    {
        $title = 'Admin';
        $description = "Gérez les utilisateurs, les contenus et les paramètres du site avec facilité. Utilisez les outils ci-dessous pour administrer toutes les sections de votre application.";

        $content = compact('title', 'description');
        return new Response(
            200,
            [],
            $this->render(
                '@Home/admin',
                $content
            )
        );
    }
}
