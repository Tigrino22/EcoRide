<?php

namespace Tigrino\Auth\Entity;

use Tigrino\Auth\Entity\User;

class SuperUser extends User
{
    public function hasRole(string $role): bool
    {
        return true;
    }
}
