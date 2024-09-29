<?php

namespace Tigrino\Auth\Entity;

use Tigrino\Auth\Entity\User;

class GuestUser extends User
{
    public function __construct(
        $id = 0,
        string $username = "guest",
        string $password = "test",
        array $roles = ['guest'],
        ?string $email = null,
        ?string $sessionToken = null,
        ?string $lastLogin = null
    ) {
        parent::__construct($id, $username, $password, $roles, $email, $sessionToken, $lastLogin);
    }
}
