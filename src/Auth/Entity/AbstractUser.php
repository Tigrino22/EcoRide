<?php

namespace Tigrino\Auth\Entity;

use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class AbstractUser implements UserInterface
{
    protected string $username;
    protected string $password;
    private UuidInterface $uuid;

    public function __construct(string $username, string $password)
    {
        $this->uuid = Uuid::uuid4();
        $this->username = $username;
        $this->password = password_hash($password, PASSWORD_BCRYPT);
    }

    public function getUuid(): UuidInterface
    {
        return $this->uuid;
    }

    public function setUuid(UuidInterface $uuid): void
    {
        $this->uuid = $uuid;
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

    public function setPassword(string $hasedPassword): bool
    {
        $this->password = $hasedPassword;
        return true;
    }
}
