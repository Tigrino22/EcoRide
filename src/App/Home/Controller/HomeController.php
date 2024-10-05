<?php

namespace Tigrino\App\Home\Controller;

use GuzzleHttp\Psr7\Response;
use Tigrino\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

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
}
