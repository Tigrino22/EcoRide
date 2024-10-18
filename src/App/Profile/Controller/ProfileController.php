<?php

namespace Tigrino\App\Profile\Controller;

use GuzzleHttp\Psr7\Response;
use Tigrino\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

class ProfileController extends AbstractController
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

