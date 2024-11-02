<?php

namespace Tigrino\App\Profile\Repository;

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

    public function getCarsByUserId(UuidInterface $user_id)
    {
        $statement = "
        SELECT * FROM cars WHERE user_id = :user_id
        ";

        return $this->db->query($statement, ['user_id' => $user_id]);
    }

    public function getCarById(UuidInterface $car_id)
    {
        $statement = "
        SELECT * FROM cars WHERE id = :car_id
        ";

        return $this->db->query($statement, ['car_id' => $car_id]);
    }

    public function insertCar(CarEntity $car): bool
    {
        $statement = "
            INSERT INTO cars (
                car_id,
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
                :car_id,
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
            'car_id' => $car->getId()->getBytes(),
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

        $this->db->commit();

        return true;
    }
}
