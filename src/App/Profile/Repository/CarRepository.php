<?php

namespace Tigrino\App\Profile\Repository;

use http\Exception\RuntimeException;
use PDOException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Tigrino\App\Profile\Entity\CarEntity;
use Tigrino\Core\Database\Database;
use Tigrino\Core\Errors\ErrorHandler;

class CarRepository
{
    private Database $db;

    public function __construct(Database $db = null)
    {
        $this->db = $db ?? new Database();
    }

    /**
     * @param UuidInterface $user_id
     * @return array
     * @throws \Exception
     */
    public function getCarsByUserId(UuidInterface $user_id): array
    {
        $statement = "
        SELECT * FROM cars WHERE user_id = :user_id
        ";

        $result = $this->db->query($statement, ['user_id' => $user_id->getBytes()]);

        return $this->convertIdToUuid($result, true);
    }

    /**
     * @param UuidInterface $car_id
     * @return CarEntity
     * @throws \Exception
     */
    public function getCarById(UuidInterface $car_id): CarEntity
    {
        $statement = "
        SELECT * FROM cars WHERE id = :car_id
        ";

        $result = $this->db->query($statement, ['car_id' => $car_id->getBytes()]);

        if (count($result) > 1) {
            throw new RuntimeException('Impossible de terminer quelle voiture');
        }

        $result = $this->convertIdToUuid($result);

        $car = CarEntity::fromArray($result[0]);

        return $car;
    }

    /**
     * Enregistrement d'une voiture en base de donnée.
     *
     * @param CarEntity $car
     * @return bool
     */
    public function insertCar(CarEntity $car): bool
    {
        $statement = "
            INSERT INTO cars (
                id,
                user_id, 
                plate_of_registration,
                first_registration_at,
                brand,
                model,
                color,
                places,
                preferences,
                created_at,
                updated_at
            ) VALUES (
                :id,
                :user_id,
                :plate_of_registration,
                :first_registration_at,
                :brand,
                :model,
                :color,
                :places,
                :preferences,
                :created_at,
                :updated_at
            )
        ";

        $params = [
            'id' => $car->getId()->getBytes(),
            'user_id' => $car->getUserId()->getBytes(),
            'plate_of_registration' => $car->getPlateOfRegistration(),
            'first_registration_at' => $car->getFirstRegistrationAt()->format('Y-m-d'),
            'brand' => $car->getBrand(),
            'model' => $car->getModel(),
            'color' => $car->getColor(),
            'places' => $car->getPlaces(),
            'preferences' => $car->getPreferences(),
            'created_at' => $car->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $car->getUpdatedAt()->format('Y-m-d H:i:s')
        ];

        $this->db->beginTransaction();
        try {
            $result = $this->db->execute($statement, $params);
            if (!$result) {
                throw new \RuntimeException('Erreur lors de l\'insertion de la voiture.');
            }
        } catch (\Exception $e) {
            ErrorHandler::logMessage("Erreur lors de l'insertion en base de données : " . $e->getMessage(), "ERROR");

            $this->db->rollBack();
            return false;
        }

        return $this->db->commit();
    }

    /**
     * Met à jour une voiture dans la base de données.
     *
     * @param CarEntity $car L'objet CarEntity contenant les nouvelles données.
     * @return bool Retourne true si la mise à jour a réussi, false sinon.
     */
    public function updateCar(CarEntity $car): bool
    {
        $statement = "
            UPDATE cars SET
                plate_of_registration = :plate_of_registration,
                first_registration_at = :first_registration_at,
                brand = :brand,
                model = :model,
                color = :color,
                places = :places,
                preferences = :preferences,
                updated_at = :updated_at
            WHERE id = :id
        ";

        $params = [
            'plate_of_registration' => $car->getPlateOfRegistration(),
            'first_registration_at' => $car->getFirstRegistrationAt()->format('Y-m-d'),
            'brand' => $car->getBrand(),
            'model' => $car->getModel(),
            'color' => $car->getColor(),
            'places' => $car->getPlaces(),
            'preferences' => $car->getPreferences(),
            'updated_at' => $car->getUpdatedAt()->format('Y-m-d H:i:s'),
            'id' => $car->getId()->getBytes() // UUID en binaire
        ];

        $this->db->beginTransaction();
        try {
            // Exécuter la requête avec les paramètres
            $this->db->execute($statement, $params);
        } catch (\PDOException $e) {
            $this->db->rollBack();
            ErrorHandler::logMessage($e, "ERROR");
            return false;
        }

        return $this->db->commit();
    }

    /**
     * Supprime un véhicule de la BDD via son UUID
     *
     * @param UuidInterface $car_id
     * @return bool
     */
    public function deleteCar(UuidInterface $car_id): bool
    {
        $statement = "
            DELETE FROM cars WHERE id = :car_id
        ";

        $params = [
            'car_id' => $car_id->getBytes()
        ];

        $this->db->beginTransaction();
        try {
            $this->db->execute($statement, $params);
        } catch (\PDOException $e) {
            ErrorHandler::logMessage(
                "Impossible de supprimer le véhicule {$car_id->toString()} : " . $e->getMessage(),
                "ERROR"
            );
            $this->db->rollBack();
            return false;
        }

        return $this->db->commit();
    }

    private function convertIdToUuid(array $cars, bool $string = false): array
    {
        if ($string) {
            foreach ($cars as $key => $car) {
                $cars[$key]['id'] = Uuid::fromBytes($car['id'])->toString();
                $cars[$key]['user_id'] = Uuid::fromBytes($car['user_id'])->toString();
            }
        } else {
            foreach ($cars as $key => $car) {
                $cars[$key]['id'] = Uuid::fromBytes($car['id']);
                $cars[$key]['user_id'] = Uuid::fromBytes($car['user_id']);
            }
        }

        return $cars;
    }
}
