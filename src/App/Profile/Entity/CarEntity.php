<?php

namespace Tigrino\App\Profile\Entity;

use AllowDynamicProperties;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[AllowDynamicProperties] class CarEntity
{
    public function __construct(
        UuidInterface $id,
        UuidInterface $user_id,
        string $plateOfRegistration,
        \DateTime $firstRegistrationAt,
        string $brand,
        string $model,
        string $color,
        int $places,
        string $preferences,
        \DateTime $createdAt,
        \DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->plateOfRegistration = $plateOfRegistration;
        $this->firstRegistrationAt = $firstRegistrationAt;
        $this->brand = $brand;
        $this->model = $model;
        $this->color = $color;
        $this->places = $places;
        $this->preferences = $preferences;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    /**
     * Instancie un objet Car depuis un tableau passé en paramètres
     *
     * @param array $data
     * @return self
     * @throws \Exception
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'],
            $data['user_id'],
            $data['plate_of_registration'],
            new \DateTime($data['first_registration_at']),
            $data['brand'],
            $data['model'],
            $data['color'],
            $data['places'],
            $data['preferences'],
            new \DateTime($data['created_at']),
            new \DateTime($data['updated_at'])
        );
    }

    /**
     * @return string
     */
    public function getBrand(): string
    {
        return $this->brand;
    }

    /**
     * @param string $brand
     */
    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    /**
     * @return string
     */
    public function getColor(): string
    {
        return $this->color;
    }

    /**
     * @param string $color
     */
    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getFirstRegistrationAt(): \DateTime
    {
        return $this->firstRegistrationAt;
    }

    /**
     * @param \DateTime $firstRegistrationAt
     */
    public function setFirstRegistrationAt(\DateTime $firstRegistrationAt): void
    {
        $this->firstRegistrationAt = $firstRegistrationAt;
    }

    /**
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * @param string $model
     */
    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * @return int
     */
    public function getPlaces(): int
    {
        return $this->places;
    }

    /**
     * @param int $places
     */
    public function setPlaces(int $places): void
    {
        $this->places = $places;
    }

    /**
     * @return string
     */
    public function getPlateOfRegistration(): string
    {
        return $this->plateOfRegistration;
    }

    /**
     * @param string $plateOfRegistration
     */
    public function setPlateOfRegistration(string $plateOfRegistration): void
    {
        $this->plateOfRegistration = $plateOfRegistration;
    }

    /**
     * @return string
     */
    public function getPreferences(): string
    {
        return $this->preferences;
    }

    /**
     * @param string $preferences
     */
    public function setPreferences(string $preferences): void
    {
        $this->preferences = $preferences;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt(): \DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt(\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return UuidInterface
     */
    public function getUserId(): UuidInterface
    {
        return $this->user_id;
    }

    /**
     * @param UuidInterface $user_id
     */
    public function setUserId(UuidInterface $user_id): void
    {
        $this->user_id = $user_id;
    }

    /**
     * @return UuidInterface
     */
    public function getId(): UuidInterface
    {
        return $this->id;
    }

    /**
     * @param UuidInterface $id
     */
    public function setId(UuidInterface $id): void
    {
        $this->uuid = $id;
    }
}
