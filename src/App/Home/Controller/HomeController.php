<?php

namespace Tigrino\App\Home\Controller;

use GuzzleHttp\Psr7\Response;
use Tigrino\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class HomeController extends AbstractController
{
    public function index(): ResponseInterface
    {
        $content = $this->render('@home/home');

        return new Response(
            200,
            [],
            $content
        );
    }
}
