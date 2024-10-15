<?php

namespace Tigrino\Errors\Controller;

use Tigrino\Core\Controller\AbstractController;
use Tigrino\Http\Errors\CodeError;
use Tigrino\Http\Errors\ForbiddenResponse;

class ErrorController extends AbstractController
{
    public function error403()
    {
        $title = 'Access Denied, error : ' . CodeError::FORBIDDEN;
        $message = "Vous n'avez pas les accès requis pour accéder à cette page";

        $params = compact('title', 'message');

        $content = $this->render('@Errors/forbidden', $params);

        return ForbiddenResponse::create($content);
    }
}
