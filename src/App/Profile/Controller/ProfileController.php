<?php

namespace Tigrino\App\Profile\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tigrino\App\Profile\Repository\UserEcorideRepository;
use Tigrino\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Services\SerializerService;

class ProfileController extends AbstractController
{
    /**
     * Récupération de l'utilisateur via UUID
     * Transformation de l'objet en tableau
     * via le service de serialiszation et la méthode objectToArray
     *
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function index(): ResponseInterface
    {

        /** @var SessionManager $session */
        $session = $this->container->get(SessionManager::class);

        $profile = $session->get('user');
        /** @var UserEcorideRepository $repository */
        $repository = $this->container->get(UserEcorideRepository::class);

        $user = $repository->findById($profile['id']);

        /** @var SerializerService $serializer */
        $serializer = $this->container->get(SerializerService::class);
        $user = $serializer->objectToArray($user);

        $session->set('user', $user);

        $content = $this->render('@Profile/Profile');

        return new Response(
            200,
            [],
            $content
        );
    }
}
