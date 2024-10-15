<?php

namespace Auth\Entity;

use PHPUnit\Framework\TestCase;
use Tigrino\Auth\Entity\GuestUser;

class GuestUserTest extends TestCase
{
    private GuestUser $guestUser;

    protected function setUp(): void
    {
        $this->guestUser = new GuestUser();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(GuestUser::class, $this->guestUser);
    }
}