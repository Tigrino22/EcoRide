<?php

namespace Tigrino\Auth\Entity;

use Ramsey\Uuid\UuidInterface;

interface UserInterface
{
    public function getUuid(): UuidInterface;
    public function setUuid(UuidInterface $uuid): void;

    public function getUsername(): string;
    public function setUsername(string $username): void;

    public function getPassword(): string;
    public function setPassword(string $password): void;
}
