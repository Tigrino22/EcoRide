<?php

namespace Tigrino\App\Ecoride\Controller;

use GuzzleHttp\Psr7\Response;
use Tigrino\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;

class CovoiturageController extends AbstractController
{
    public function index(): ResponseInterface
    {
        $content = $this->render('@Covoiturage/covoiturage');

        return new Response(
            200,
            [],
            $content
        );
    }

    public function searchCovoiturage(): ResponseInterface
    {
        $search = true;

        // Reception de la requete
        // Recuperation des paramêtre
        // Recherche en BDD
        // Compact des information
        // Preparation de le vue
        // Retour de la vue avec les covoiturage récupérés.

        $covoiturages = null;

        $params = compact('covoiturages', 'search');

        $content = $this->render('@Covoiturage/covoiturage', $params);

        return new Response(
            200,
            [],
            $content
        );
    }
}
