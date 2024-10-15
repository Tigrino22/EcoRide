<?php

namespace Tigrino\App\Ecoride\Entity;

use DateTime;
use Tigrino\Auth\Entity\User;

class UserEcoride extends User
{
    private string $name;
    private string $firstname;
    private ?string $telephone;
    private ?string $address;
    private ?string $birthday;
    private ?string $photo;
    private ?string $created_at;
    private ?string $updated_at;

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
        $this->telephone = $data['telephone'] ?? null;
        $this->address = $data['address'] ?? null;
        $this->birthday = $data['birthday'] ?? null;
        $this->photo = $data['photo'] ?? null;
        $this->created_at = $data['created_at'] ?? null;
        $this->updated_at = $data['updated_at'] ?? null;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function getTelephone()
    {
        return $this->telephone;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function getBirthday()
    {
        return $this->birthday;
    }

    public function getPhoto()
    {
        return $this->photo;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function setFirstname($firstname): void
    {
        $this->firstname = $firstname;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function setTelephone($telephone): void
    {
        $this->telephone = $telephone;
    }

    public function setAddress($address): void
    {
        $this->address = $address;
    }

    public function setBirthday($birthday): void
    {
        $this->birthday = $birthday;
    }

    public function setPhoto($photo): void
    {
        $this->photo = $photo;
    }

    public function setCreatedAt($created_at): void
    {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt($updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}
