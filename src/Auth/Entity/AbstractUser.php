<?php

namespace Tigrino\Auth\Entity;

use Tigrino\Auth\Entity\UserInterface;

class AbstractUser implements UserInterface
{
    protected $uuid;
    protected string $username;
    protected string $password;

    public function __construct($uuid, string $username, string $password)
    {
        $this->uuid = $uuid;
        $this->username = $username;
        $this->password = $password;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function setUuid(string $uuid): void
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
