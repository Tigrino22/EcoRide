<?php

namespace Tigrino\App\Ecoride\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Core\Session\SessionManager;

class HomeController extends AbstractController
{
    public function index(): ResponseInterface
    {
        $content = $this->render('@Home/home');

//        var_dump($this->container->get(SessionManager::class)->get('user'));
//        die();

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
        $description =
            "Gérez les utilisateurs, 
            les contenus et les paramètres du site avec facilité. 
            Utilisez les outils ci-dessous pour administrer 
            toutes les sections de votre application.";

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
