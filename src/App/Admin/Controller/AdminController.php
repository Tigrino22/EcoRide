<?php

namespace Tigrino\App\Admin\Controller;

use GuzzleHttp\Psr7\Response;
use Tigrino\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

class AdminController extends AbstractController
{
    public function index(): ResponseInterface
    {
          $content = $this->render('@Admin/Admin');

          return new Response(
              200,
              [],
              $content
          );
    }
}
