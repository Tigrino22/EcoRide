<?php

namespace Tigrino\App\Profile\Entity;

use AllowDynamicProperties;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

#[AllowDynamicProperties]
class CarEntity
{
    private UuidInterface $id;
    private UuidInterface $user_id;
    private string $plateOfRegistration;
    private \DateTime $firstRegistrationAt;
    private string $brand_name;
    private UuidInterface $brand_id;
    private string $model;
    private string $color;
    private int $places;
    private string $preferences;
    private \DateTime $createdAt;
    private \DateTime $updatedAt;
    private string $energie_name;
    private UuidInterface $energie_id;

    public function __construct(
        UuidInterface $id,
        UuidInterface $user_id,
        string $plateOfRegistration,
        \DateTime $firstRegistrationAt,
        string $brand_name,
        UuidInterface $brand_id,
        string $model,
        string $color,
        int $places,
        string $preferences,
        \DateTime $createdAt,
        \DateTime $updatedAt,
        string $energie_name,
        UuidInterface $energie_id
    ) {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->plateOfRegistration = $plateOfRegistration;
        $this->firstRegistrationAt = $firstRegistrationAt;
        $this->brand_name = $brand_name;
        $this->brand_id = $brand_id;
        $this->model = $model;
        $this->color = $color;
        $this->places = $places;
        $this->preferences = $preferences;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->energie_name = $energie_name;
        $this->energie_id = $energie_id;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] instanceof UuidInterface ? $data['id'] : Uuid::fromString($data['id']),
            $data['user_id'] instanceof UuidInterface ? $data['user_id'] : Uuid::fromString($data['user_id']),
            $data['plate_of_registration'],
            new \DateTime($data['first_registration_at']),
            $data['brand_name'],
            $data['brand_id'],
            $data['model'],
            $data['color'],
            (int)$data['places'],
            $data['preferences'] ?? '',
            new \DateTime($data['created_at']),
            new \DateTime($data['updated_at']),
            $data['energie_name'],
            $data['energie_id']
        );
    }

    /**
     * @return string
     */
    public function getBrandName(): string
    {
        return $this->brand_name;
    }

    /**
     * @param string $brand
     */
    public function setBrandName(string $brand): void
    {
        $this->brand_name = $brand;
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

    public function getEnergieName(): string
    {
        return $this->energie_name;
    }

    public function setEnergieName(string $energie): void
    {
        $this->energie_name = $energie;
    }

    public function getBrandId(): UuidInterface
    {
        return $this->brand_id;
    }

    public function setBrandId(UuidInterface $brand_id): void
    {
        $this->brand_id = $brand_id;
    }

    public function getEnergieId(): UuidInterface
    {
        return $this->energie_id;
    }

    public function setEnergieId(UuidInterface $energie_id): void
    {
        $this->energie_id = $energie_id;
    }
}
