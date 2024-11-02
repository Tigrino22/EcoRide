<?php

namespace Tigrino\App\Profile\Controller;

use Exception;
use GuzzleHttp\Psr7\Response;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Ramsey\Uuid\Uuid;
use Tigrino\App\Profile\Entity\CarEntity;
use Tigrino\App\Profile\Repository\CarRepository;
use Tigrino\App\Profile\Services\CarValidator;
use Tigrino\Core\Controller\AbstractController;
use Tigrino\Core\Errors\ErrorHandler;
use Tigrino\Core\Router\Router;
use Tigrino\Core\Session\SessionManager;
use Tigrino\Http\Response\RedirectResponse;

class CarController extends AbstractController
{
    public function show(): ResponseInterface
    {

        $session = new SessionManager();

        $repository = new CarRepository();

        return new Response(
            200,
            [],
            $this->render('@Profile/Car/show')
        );
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function insert(): ResponseInterface
    {
        $session = new SessionManager();
        $repository = new CarRepository();

        if ($this->request->getMethod() == 'POST') {
            $data = $this->request->getParsedBody();
            $data = CarValidator::validate($data);

            if (isset($data['errors'])) {
                // Render si des errors sont présentes
                return new Response(
                    400,
                    [],
                    $this->render('@Profile/Car/insert', ['errors' => $data['errors']])
                );
            }

            $data = $data['data'];

            $data['id'] = Uuid::uuid4();
            $data['user_id'] = $session->get('user')['id'];
            $data['createdAt'] = date('Y-m-d H:i:s');
            $data['updatedAt'] = date('Y-m-d H:i:s');

            try {
                $car = CarEntity::fromArray($data);
                $repository->insertCar($car);
            } catch (Exception $e) {
                ErrorHandler::logMessage("Erreur lors de l'insertion du véhicule : " . $e->getMessage(), "ERROR");

                return new Response(
                    500,
                    [],
                    "Une erreur est survenue lors de l'insertion du véhicule."
                );
            }

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
}
