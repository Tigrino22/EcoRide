<?php

namespace Tigrino\Errors\Controller;

use Psr\Http\Message\ResponseInterface;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Http\Errors\ForbiddenResponse;
use Tigrino\Http\Errors\NotFoundResponse;

class ErrorController extends AbstractController
{
    public function error403(): ResponseInterface
    {
        $title = 'Access Denied';
        $message = "Vous n'avez pas les accès requis pour accéder à cette page";

        $params = compact('title', 'message');

        $content = $this->render('@Errors/forbidden', $params);

        return ForbiddenResponse::create($content);
    }

    public function error404(): ResponseInterface
    {
        $title = 'Page Not Found';
        $message = "Vous vous êtes éloigné de la route.\nRetourner sur les sentiers balisés.";

        $params = compact('title', 'message');

        $content = $this->render('@Errors/notFound', $params);

        return NotFoundResponse::create($content);
    }
}
