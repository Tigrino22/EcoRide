<?php

namespace Tigrino\App\Ecoride\Controller;

use GuzzleHttp\Psr7\Response;
use Tigrino\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

class CovoiturageController extends AbstractController
{
    public function index(): ResponseInterface
    {

        $search = true;
        $datas = compact('search');
        $content = $this->render('@Covoiturage/covoiturage', $datas);

        return new Response(
            200,
            [],
            $content
        );
    }
}
