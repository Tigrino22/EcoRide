<?php

namespace Tests\Core\Controllers;

use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\ServerRequest;
use Psr\Http\Message\ResponseInterface;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Http\Response\JsonResponse;

class TestController extends AbstractController
{
    public function index(): ResponseInterface
    {
        return new Response(200, [], "Hello test");
    }

    public function show(int $id, string $slug): ResponseInterface
    {
        return new Response(200, [], "Hello test {$id}-{$slug}");
    }

    public function admin(): ResponseInterface
    {
        return new Response(200, [], "Hello admin");
    }

    /**
     * Méthode pour traiter les requêtes POST.
     *
     * @param ServerRequest $request
     * @return ResponseInterface
     */
    public function create(ServerRequest $request): ResponseInterface
    {
        // Récupérer les données POST
        $data = $request->getParsedBody();

        // Traitement des données (ex. : enregistrement dans une base de données)
        // Ici, nous renvoyons simplement les données reçues pour démonstration

        return new JsonResponse(data: [
            "message" => "Données reçues avec succès",
            "data" => $data
        ]);
    }

    public function AbstractControllerTestMethod(): ResponseInterface
    {
        return new Response(200, [], "Test réussi");
    }

    /**
     * 403 pour test AbtractController
     *
     * @return ResponseInterface
     */
    public function forbiddenTest(): ResponseInterface
    {
        // Simuler une réponse HTTP
        return new Response(403, [], 'forbidden');
    }

    /**
     * 404 pour test AbtractController
     *
     * @return ResponseInterface
     */
    public function notFoundTest(): ResponseInterface
    {
        // Simuler une réponse HTTP
        return new Response(404, [], 'not found');
    }
}
