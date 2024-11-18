<?php

namespace Tigrino\App\Profile\Repository;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Tigrino\App\Profile\Entity\CarEntity;
use Tigrino\Core\Database\Database;

class CarRepository
{
    private Database $db;

    public function __construct(Database $db = null)
    {
        $this->db = $db ?? new Database();
    }

    /**
     * Retourne un tableau de CarEntity
     * @param UuidInterface $user_id
     * @return CarEntity[]|false
     */
    public function getCarsByUserId(UuidInterface $user_id): array|false
    {

        $statement = "
                    SELECT 
                        cars.*,
                        brands.name as brand_name,
                        energies.name as energie_name
                    FROM cars 
                    JOIN brands ON cars.brand_id = brands.id
                    JOIN energies ON cars.energie_id = energies.id
                    WHERE user_id = :user_id";
        $result = $this->db->query($statement, ['user_id' => $user_id->getBytes()]);

        if ($result) {
            return array_map(fn($row) => CarEntity::fromArray($this->convertIdToUuid($row)), $result);
        }
        return false;
    }

    public function getCarById(UuidInterface $car_id): CarEntity
    {
        $statement = "
                    SELECT 
                        cars.*,
                        brands.name as brand_name,
                        energies.name as energie_name
                    FROM cars 
                    JOIN brands ON cars.brand_id = brands.id
                    JOIN energies ON cars.energie_id = energies.id
                WHERE cars.id = :car_id";

        $result = $this->db->query($statement, ['car_id' => $car_id->getBytes()]);

        if (count($result) !== 1) {
            throw new \RuntimeException('Car not found or ambiguous result');
        }

        return CarEntity::fromArray($this->convertIdToUuid($result[0]));
    }

    public function getCarByPlate(string $plate): CarEntity
    {
        $statement = "
                    SELECT 
                        cars.*,
                        brands.name as brand_name,
                        energies.name as energie_name
                    FROM cars 
                    JOIN brands ON cars.brand_id = brands.id
                    JOIN energies ON cars.energie_id = energies.id
                WHERE cars.plate_of_registration = :plate_of_registration";

        $result = $this->db->query($statement, ['plate_of_registration' => $plate]);
        if (count($result) !== 1) {
            throw new \RuntimeException('Car not found or ambiguous result');
        }

        return CarEntity::fromArray($this->convertIdToUuid($result[0]));
    }

    public function insertCar(CarEntity $car): bool
    {
        $statement = "INSERT INTO cars (
                    id, 
                    user_id, 
                    plate_of_registration, 
                    first_registration_at, 
                    brand_id,
                    model, 
                    color, 
                    places, 
                    preferences,
                    created_at, 
                    updated_at,
                    energie_id
                ) VALUES (
                    :id, 
                    :user_id, 
                    :plate_of_registration, 
                    :first_registration_at, 
                    :brand_id,
                    :model, 
                    :color, 
                    :places, 
                    :preferences, 
                    :created_at, 
                    :updated_at, 
                    :energie_id
        )";

        $params = [
            'id' => $car->getId()->getBytes(),
            'user_id' => $car->getUserId()->getBytes(),
            'plate_of_registration' => $car->getPlateOfRegistration(),
            'first_registration_at' => $car->getFirstRegistrationAt()->format('Y-m-d'),
            'brand_id' => $car->getBrandId()->getBytes(),
            'model' => $car->getModel(),
            'color' => $car->getColor(),
            'places' => $car->getPlaces(),
            'preferences' => $car->getPreferences(),
            'created_at' => $car->getCreatedAt()->format('Y-m-d H:i:s'),
            'updated_at' => $car->getUpdatedAt()->format('Y-m-d H:i:s'),
            'energie_id' => $car->getEnergieId()->getBytes()
        ];

        return $this->db->execute($statement, $params);
    }

    public function updateCar(CarEntity $car): bool
    {
        $statement = "UPDATE cars SET
            plate_of_registration = :plate_of_registration,
            first_registration_at = :first_registration_at,
            brand_id = :brand_id,
            model = :model,
            color = :color,
            places = :places,
            preferences = :preferences,
            updated_at = :updated_at,
            energie_id = :energie_id
        WHERE id = :id";

        $params = [
            'id' => $car->getId()->getBytes(),
            'plate_of_registration' => $car->getPlateOfRegistration(),
            'first_registration_at' => $car->getFirstRegistrationAt()->format('Y-m-d'),
            'brand_id' => $car->getBrandId()->getBytes(),
            'model' => $car->getModel(),
            'color' => $car->getColor(),
            'places' => $car->getPlaces(),
            'preferences' => $car->getPreferences(),
            'updated_at' => $car->getUpdatedAt()->format('Y-m-d H:i:s'),
            'energie_id' => $car->getEnergieId()->getBytes()
        ];

        return $this->db->execute($statement, $params);
    }

    public function getBrands(): array
    {
        $query = 'SELECT BIN_TO_UUID(id) as id, name FROM brands';

        return $this->db->query($query);
    }

    public function getBrandById(UuidInterface $id): array
    {
        $query = 'SELECT BIN_TO_UUID(id) as id, name FROM brands WHERE id = :id';

        return $this->db->query($query, ['id' => $id->getBytes()]);
    }

    public function getEnergies(): array
    {
        $query = 'SELECT BIN_TO_UUID(id) as id, name FROM energies';

        return $this->db->query($query);
    }

    public function getEnergieById(UuidInterface $id): array
    {
        $query = 'SELECT BIN_TO_UUID(id) as id, name FROM energies WHERE id = :id';

        return $this->db->query($query, ['id' => $id->getBytes()]);
    }

    public function deleteCar(UuidInterface $id): bool
    {
        $statement = "DELETE FROM cars WHERE id = :id";

        $params = [
            'id' => $id->getBytes()
        ];

        $this->db->beginTransaction();
        if ($this->db->execute($statement, $params)) {
            $this->db->commit();
            return true;
        }
        $this->db->rollBack();
        return false;
    }

    private function convertIdToUuid(array $row, bool $string = false): array
    {
        if ($string) {
            $row['id'] = Uuid::fromBytes($row['id'])->toString();
            $row['user_id'] = Uuid::fromBytes($row['user_id'])->toString();
            $row['brand_id'] = Uuid::fromBytes($row['brand_id'])->toString();
            $row['energie_id'] = Uuid::fromBytes($row['energie_id'])->toString();
            return $row;
        }

        $row['id'] = Uuid::fromBytes($row['id']);
        $row['user_id'] = Uuid::fromBytes($row['user_id']);
        $row['brand_id'] = Uuid::fromBytes($row['brand_id']);
        $row['energie_id'] = Uuid::fromBytes($row['energie_id']);
        return $row;
    }
}
