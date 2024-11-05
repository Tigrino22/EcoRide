<?php

namespace Tigrino\App\Profile\Controller;

use AllowDynamicProperties;
use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Tigrino\App\Profile\Entity\CarEntity;
use Tigrino\App\Profile\Repository\CarRepository;
use Tigrino\App\Profile\Services\CarValidator;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Core\Errors\ErrorHandler;
use Tigrino\Core\Misc\VarDumper;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Http\Errors\ForbiddenResponse;
use Tigrino\Http\Errors\UnauthorizedMethodResponse;
use Tigrino\Http\Response\RedirectResponse;
use Tigrino\Services\FlashService;

#[AllowDynamicProperties]
class CarController extends AbstractController
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        /** @var CarRepository repository */
        $this->repository = $this->container->get(CarRepository::class);
        /** @var SessionManager session */
        $this->session = $this->container->get(SessionManager::class);
        /** @var FlashService flashService */
        $this->flashService = $this->container->get(FlashService::class);
    }

    /**
     * @return ResponseInterface
     */
    public function show(): ResponseInterface
    {
        $cars = $this->repository->getCarsByUserId($this->session->get('user')['id']);

        return new Response(
            200,
            [],
            $this->render('@Profile/Car/show', ['cars' => $cars])
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function insert(): ResponseInterface
    {
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getParsedBody();
            $data = CarValidator::validate($data);

            if (isset($data['errors'])) {
                foreach ($data['errors'] as $error) {
                    $this->flashService->add('error', $error);
                }

                // Render si des errors sont présentes
                return new Response(
                    400,
                    [],
                    $this->render('@Profile/Car/insert')
                );
            }

            $data['id'] = Uuid::uuid4();
            $data['user_id'] = $this->session->get('user')['id'];
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['updated_at'] = date('Y-m-d H:i:s');

            try {
                $car = CarEntity::fromArray($data);
                $this->repository->insertCar($car);
            } catch (Exception $e) {
                ErrorHandler::logMessage("Erreur lors de l'insertion du véhicule : " . $e->getMessage(), "ERROR");

                return new Response(
                    500,
                    [],
                    "Une erreur est survenue lors de l'insertion du véhicule."
                );
            }
            $this->flashService->add('success', 'Le véhicule a correctement été ajouté.');
            return RedirectResponse::create(
                $this->container->get(Router::class)->generate('car.show')
            );
        }

        return new Response(
            200,
            [],
            $this->render('@Profile/Car/insert')
        );
    }

    /**
     * @param $id
     * @return ResponseInterface
     */
    public function update($id): ResponseInterface
    {
        /** @var CarEntity $car */
        $car = $this->repository->getCarById(Uuid::fromString($id));

        $this->checkUser($car->getId());

        /**
         * Gestion de la soumission du formulaire
         *
         */
        if ($this->request->getMethod() === 'POST') {
            $data = $this->request->getParsedBody();
            $data = CarValidator::validate($data);

            if (isset($data['errors'])) {
                foreach ($data['errors'] as $error) {
                    $this->flashService->add('error', $error);
                }

                return new Response(
                    400,
                    [],
                    $this->render('@Profile/Car/update', ['car' => $car])
                );
            }

            $data['id'] = Uuid::fromString($id);
            $data['user_id'] = $this->session->get('user')['id'];
            $data['updated_at'] = date('Y-m-d H:i:s');
            $data['created_at'] = $car->getCreatedAt()->format('Y-m-d H:i:s');
            $carUpdated = CarEntity::fromArray($data);

            if ($this->repository->updateCar($carUpdated)) {
                $this->flashService->add('success', 'Le véhicule a correctement été mis à jour.');
                return RedirectResponse::create(
                    $this->container->get(Router::class)->generate('car.show')
                );
            } else {
                $this->flashService->add('error', 'Une erreur est survenue durant la mise à jour.');

                return new Response(
                    400,
                    [],
                    $this->render('@Profile/Car/update')
                );
            }
        }

        /**
         * Affichage de la page en GET
         *
         */

        return new Response(
            200,
            [],
            $this->render('@Profile/Car/update', ['car' => $car])
        );
    }

    /**
     * @param $id
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function delete($id): ResponseInterface
    {
        if ($this->request->getMethod() === 'POST' && $this->request->getParsedBody()['_method'] === 'DELETE') {
            $this->checkUser(Uuid::fromString($id));
            $deleted = $this->repository->deleteCar(Uuid::fromString($id));

            if ($deleted) {
                $this->flashService->add('success', 'Le véhicule a été supprimé avec succès.');
                return RedirectResponse::create(
                    $this->container->get(Router::class)->generate('car.show')
                );
            } else {
                $this->flashService->add('error', 'Erreur lors de la suppression du véhicule.');
                return RedirectResponse::create(
                    $this->container->get(Router::class)->generate('car.show')
                );
            }
        }

        return RedirectResponse::create(
            $this->container->get(Router::class)->generate('error.405')
        );
    }

    private function checkUser(UuidInterface $car_id)
    {
        /**
         * Si l'utilisateur n'a pas accès a ce véhicule.
         * Non connecté ou Non propriétaire.
         *
         */
        if ($car_id->toString() !== $this->session->get('user')['id']->toString()) {
            return ForbiddenResponse::create(
                $this->render('@Errors/forbidden')
            );
        }
    }
}
