<?php

namespace Tigrino\App\Profile\Entity;

use Ramsey\Uuid\UuidInterface;
use Tigrino\Auth\Entity\User;

class UserEcoride extends User
{
    private ?string $name;
    private ?string $firstname;
    private ?string $phone;
    private ?string $address;
    private ?int $postal_code;
    private ?string $city;
    private ?string $birthday;
    private mixed $photo;
    private ?string $created_at;
    private ?string $updated_at;
    private bool $is_driver;
    private bool $is_passenger;
    private ?UuidInterface $solde_id;
    private ?UuidInterface $configuration_id;

    public function __construct(array $data = [])
    {

        parent::__construct(
            username: $data['username'],
            password: $data['password'],
            email: $data['email'] ?? null,
            lastLogin: $data['lastLogin'] ?? null
        );

        $this->name = $data['name'] ?? null;
        $this->firstname = $data['firstname'] ?? null;
        $this->phone = $data['phone'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->postal_code = $data['postal_code'] ?? null;
        $this->city = $data['city'] ?? null;
        $this->birthday = $data['birthday'] ?? null;
        $this->photo = $data['photo'] ?? null;
        $this->is_passenger = $data['is_passenger'] ?? true;
        $this->is_driver = $data['is_driver'] ?? false;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
        $this->solde_id = $data['solde_id'] ?? null;                    // Attention de bien respecter UuidInterface
        $this->configuration_id = $data['configuration_id'] ?? null;    // Attention de bien respecter UuidInterface
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(?string $firstname): void
    {
        $this->firstname = $firstname;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getBirthday(): ?string
    {
        return $this->birthday;
    }

    public function setBirthday(?string $birthday): void
    {
        $this->birthday = $birthday;
    }

    public function getPhoto(): mixed
    {
        return $this->photo;
    }

    public function setPhoto(mixed $photo): void
    {
        $this->photo = $photo;
    }

    public function getCreatedAt(): string
    {
        return $this->created_at;
    }

    public function setCreatedAt(string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getUpdatedAt(): string
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getIsDriver(): bool
    {
        return $this->is_driver;
    }

    public function setIsDriver(bool $is_driver): void
    {
        $this->is_driver = $is_driver;
    }

    public function getIsPassenger(): bool
    {
        return $this->is_passenger;
    }

    public function setIsPassenger(bool $is_passenger): void
    {
        $this->is_passenger = $is_passenger;
    }

    public function getSoldeId(): ?UuidInterface
    {
        return $this->solde_id;
    }

    public function setSoldeId(?UuidInterface $solde_id): void
    {
        $this->solde_id = $solde_id;
    }

    public function getConfigurationId(): ?UuidInterface
    {
        return $this->configuration_id;
    }

    public function setConfigurationId(?UuidInterface $configuration_id): void
    {
        $this->configuration_id = $configuration_id;
    }

    public function getPostalCode(): ?int
    {
        return $this->postal_code;
    }

    public function setPostalCode(?int $postal_code): void
    {
        $this->postal_code = $postal_code;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(?string $city): void
    {
        $this->city = $city;
    }
}
