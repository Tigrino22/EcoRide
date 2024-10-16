<?php

namespace Tigrino\App\Profil\Controller;

use GuzzleHttp\Psr7\Response;
use Tigrino\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

class ProfilController extends AbstractController
{
    public function index(): ResponseInterface
    {
          $content = $this->render('@Profil/Profil');

          return new Response(
              200,
              [],
              $content
          );
    }
}

