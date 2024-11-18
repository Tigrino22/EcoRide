<?php

namespace App\Profile\Repository;

use Cassandra\Uuid;
use PHPUnit\Framework\TestCase;
use Tigrino\App\Profile\Entity\CarEntity;
use Tigrino\App\Profile\Repository\CarRepository;
use Tigrino\Core\Database\Database;
use Tigrino\Services\SerializerService;

class CarRepositoryTest extends TestCase
{
    private $repository;
    private $db;
    private $car;
    private $userId;

    protected function setUp(): void
    {
        $this->db = new Database('sqlite');

        $this->repository = new CarRepository($this->db);

        $this->db->execute('DROP TABLE IF EXISTS cars');


        $this->db->execute('CREATE TABLE cars (
                    id BINARY(16) NOT NULL PRIMARY KEY,
                    user_id BINARY(16) NOT NULL,
                    plate_of_registration VARCHAR(32) NOT NULL UNIQUE ,
                    first_registration_at DATE NOT NULL,
                    brand VARCHAR(32) NOT NULL ,
                    model VARCHAR(32) NOT NULL ,
                    color VARCHAR(32) NOT NULL ,
                    places TINYINT NOT NULL CHECK ( places > 0 ),
                    preferences TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE      
            )');

        $this->userId = \Ramsey\Uuid\Uuid::uuid4();

        $this->car = new CarEntity(
            \Ramsey\Uuid\Uuid::uuid4(),
            $this->userId,
            '11-ABC-11',
            new \DateTime(),
            'brand',
            'model',
            'color',
            5,
            'preferences',
            new \DateTime(),
            new \DateTime()
        );
    }

    public function tearDown(): void
    {
           $this->db->execute('DROP TABLE IF EXISTS cars');
    }

    public function testInsertTrue()
    {
        $result = $this->repository->insertCar($this->car);

        $this->assertTrue($result);
    }

    public function testUpdate()
    {
        $this->repository->insertCar($this->car);
        $this->car->setBrand('Opel');
        $result = $this->repository->updateCar($this->car);

        $this->assertTrue($result);
    }

    public function testDelete()
    {
        $this->repository->insertCar($this->car);
        $result = $this->repository->deleteCar($this->car->getId());
        $this->assertTrue($result);



    }

    public function testConvertGetById()
    {
        $this->repository->insertCar($this->car);

        $result = $this->repository->getCarById($this->car->getId());

        $this->assertInstanceOf(CarEntity::class, $result);
    }

    public function testConvertGetByUserId()
    {
        $this->repository->insertCar($this->car);

        $result = $this->repository->getCarsByUserId($this->userId);

        $this->assertIsArray($result);
    }
}
