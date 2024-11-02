<?php

namespace Tigrino\Auth\Entity;

use Ramsey\Uuid\UuidInterface;

interface UserInterface
{
    public function getId(): UuidInterface;
    public function setId(UuidInterface $uuid): void;

    public function getUsername(): string;
    public function setUsername(string $username): void;

    public function getPassword(): string;
    public function setPassword(string $password): void;
}
