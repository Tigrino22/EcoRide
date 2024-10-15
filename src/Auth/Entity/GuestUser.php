<?php

namespace Tigrino\Auth\Entity;

use Tigrino\Auth\Config\Role;
use Tigrino\Auth\Entity\User;

class GuestUser extends User
{
    public function __construct()
    {
        parent::__construct('GUEST', 'GUEST');
    }
}
