<?php

namespace Tigrino\Auth\Entity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AbstractUser implements UserInterface
{
    protected string $username;
    protected string $password;
    private UuidInterface $id;

    public function __construct(string $username, string $password)
    {
        $this->id = Uuid::uuid4();
        $this->username = $username;
        $this->password = $password;
    }

    public function getId(): UuidInterface
    {
        return $this->id;
    }

    public function setId(UuidInterface $uuid): void
    {
        $this->id = $uuid;
    }

    public function getUsername(): string
    {
        return  $this->username;
    }

    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }
}
