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
use Tigrino\Core\Misc\VarDumper;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Core\Session\SessionManagerInterface;
use Tigrino\Http\Response\RedirectResponse;
use Tigrino\Services\FlashService;
use Tigrino\Services\SerializerService;

class ProfileController extends AbstractController
{
    private SessionManagerInterface $session;
    private UserEcorideRepository $repository;
    private SerializerService $serializer;
    private mixed $flashService;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->session = $container->get(SessionManager::class);
        $this->repository = $container->get(UserEcorideRepository::class);
        $this->serializer = $container->get(SerializerService::class);
        $this->flashService = $container->get(FlashService::class);
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

        $profile = $this->session->get('user');

        $user = $this->repository->findById($profile['id']);

        $user = $this->serializer->objectToArray($user);

        $this->session->set('user', $user);

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

        if ($this->repository->update($userUpdated)) {
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
     * Fonction a implémenter qui servira à mettre à jour le status utilisateur via requete fetch
     * en appuyant sur le toggle button.
     * @param $id
     * @return ResponseInterface
     */
    public function updateDriver($id): ResponseInterface
    {
        if ($this->request->getMethod() !== 'POST') {
            return $this->index();
        }

        // Vérifiation qu'il s'agit bien du formulaire de l'user
        VarDumper::dump($this->request->getParsedBody()['is_driver']);
        die();
        // Vérification du formulaire
        //
    }
}
