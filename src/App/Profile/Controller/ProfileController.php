<?php

namespace Tigrino\App\Profile\Controller;

use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Ramsey\Uuid\Uuid;
use Tigrino\App\Profile\Entity\UserEcoride;
use Tigrino\App\Profile\Repository\UserEcorideRepository;
use Tigrino\App\Profile\Services\UserValidator;
use Tigrino\Core\Controller\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Tigrino\Core\Errors\ErrorHandler;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Core\Session\SessionManagerInterface;
use Tigrino\Http\Errors\NotFoundResponse;
use Tigrino\Http\Response\JsonResponse;
use Tigrino\Http\Response\RedirectResponse;
use Tigrino\Services\FlashService;
use Tigrino\Services\SerializerService;

class ProfileController extends AbstractController
{
    private SessionManagerInterface $session;
    private UserEcorideRepository $repository;
    private FlashService $flashService;
    private SerializerService $serializer;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->session = $container->get(SessionManager::class);
        $this->repository = $container->get(UserEcorideRepository::class);
        $this->flashService = $container->get(FlashService::class);
        $this->serializer = $container->get(SerializerService::class);
    }

    /**
     * Récupération de l'utilisateur via UUID
     * Transformation de l'objet en tableau
     * via le service de serialiszation et la méthode objectToArray
     *
     * @return ResponseInterface
     */
    public function index(): ResponseInterface
    {
        $content = $this->render('@Profile/Profile');

        return new Response(
            200,
            [],
            $content
        );
    }

    /**
     * Fonction de mise à jour d'un profile utilisateur
     *
     * @param $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function update($id): ResponseInterface
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->index();
        }

        // Vérifiation qu'il s'agit bien du formulaire de l'user
        if ($this->session->get('user')['id']->toString() !== $id) {
            return $this->index();
        }

        $data = UserValidator::validate($this->request->getParsedBody());

        if (isset($data['errors'])) {
            foreach ($data['errors'] as $error) {
                $this->flashService->add('error', $error);
            }

            return new Response(
                400,
                [],
                $this->render('@Profile/Profile')
            );
        }

        $user = $this->repository->findById(Uuid::fromString($id));

        $data['username'] = $user->getUsername();
        $data['password'] = $user->getPassword();

        $userUpdated = new UserEcoride($data);
        $userUpdated->setId($user->getId());
        $userUpdated->setUpdatedAt((new \DateTime())->format('Y-m-d H:i:s'));
        $userUpdated->setCreatedAt($user->getCreatedAt());

        if ($this->repository->update($userUpdated)) {
            $this->session->set('user', $this->serializer->objectToArray($userUpdated));
            $this->flashService->add('success', 'Votre profil a bien été mis à jour.');
            return RedirectResponse::create(
                $this->container->get(Router::class)->generate('profile')
            );
        } else {
            $this->flashService->add('error', 'Une erreur est survenue sur la mise à jour de votre profil');
            return new Response(
                400,
                [],
                $this->render('@Profile/Profile')
            );
        }
    }

    /**
     * Met à jour si l'user veut passer chauffeur
     *
     * @param $id
     * @return ResponseInterface
     */
    public function updateDriver($id): ResponseInterface
    {
        // Vérification de l'user en session
        if ($id !== ($this->session->get('user')['id']->toString())) {
            return JsonResponse::create(
                data: [
                    'message' => 'Une erreur est survenue'
                ]
            );
        }

        // Convertir l'ID en UUID
        $uuid = Uuid::fromString($id);

        // Récupérer l'utilisateur
        /** @var UserEcoride $user */
        $user = $this->repository->findById($uuid);

        if (!$user) {
            return JsonResponse::create(
                data: [
                    'error' => 'User not found',
                    'id' => $id,
                ],
                status: 404
            );
        }

        if ($user->getIsDriver()) {
            $user->setIsDriver(false);
        } else {
            $user->setIsDriver(true);
        }

        if ($this->repository->update($user)) {
            return JsonResponse::create(
                data: [
                    'id' => $id,
                    'message' => 'message provenance toggle driver',
                    'user' => $this->serializer->objectToArray($user),
                ]
            );
        } else {
            return JsonResponse::create(
                data: [
                    'error' => 'Une erreur est survenue lors de la mise a jour'
                ]
            );
        }
    }

    /**
     * Met a jour si l'user est un passager ou non
     *
     * @param $id
     * @return ResponseInterface
     */
    public function updatePassenger($id): ResponseInterface
    {
        // Vérification de l'user en session
        if ($id !== ($this->session->get('user')['id']->toString())) {
            return JsonResponse::create(
                data: [
                    'message' => 'Une erreur est survenue'
                ]
            );
        }

        // Convertir l'ID en UUID
        $uuid = Uuid::fromString($id);

        // Récupérer l'utilisateur
        /** @var UserEcoride $user */
        $user = $this->repository->findById($uuid);

        if (!$user) {
            return JsonResponse::create(
                data: [
                    'error' => 'User not found',
                    'id' => $id,
                ],
                status: 404
            );
        }

        if ($user->getIsPassenger()) {
            $user->setIsPassenger(false);
        } else {
            $user->setIsPassenger(true);
        }

        if ($this->repository->update($user)) {
            return JsonResponse::create(
                data: [
                    'id' => $id,
                    'message' => 'message provenance toggle driver',
                    'user' => $this->serializer->objectToArray($user),
                ]
            );
        } else {
            return JsonResponse::create(
                data: [
                    'error' => 'Une erreur est survenue lors de la mise a jour'
                ]
            );
        }
    }
}
