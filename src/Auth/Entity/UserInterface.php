<?php

namespace Tigrino\Auth\Entity;

interface UserInterface
{
    public function getUuid();
    public function setUuid(string $uuid): void;

    public function getUsername(): string;
    public function setUsername(string $username): void;

    public function getPassword(): string;
    public function setPassword(string $hasedPassword): bool;
}
