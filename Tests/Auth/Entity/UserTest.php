<?php

namespace Tests\Auth\Entity;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Tigrino\Auth\Entity\User;

class UserTest extends TestCase
{
    private User $user;
    private $uuid;
    public function setUp(): void
    {
        $this->uuid = Uuid::uuid4();
        $this->user = new User(
            $this->uuid,
            "userTest",
            "passwordTest",
            ['user'],
            "email@test.fr",
            "azeaze",
            "2024-09-20 16:07:30"
        );
    }

    public function testRole()
    {
        $this->assertTrue($this->user->hasRole('user'));

        $this->assertContainsEquals('user', $this->user->getRoles());
    }

    public function testEmail()
    {
        $this->user->setEmail("nouveau@test.fr");
        $this->assertEquals("nouveau@test.fr", $this->user->getEmail());
    }

    public function testSessionToken()
    {
        $this->user->setSessionToken("testtest");
        $this->assertEquals("testtest", $this->user->getSessionToken());
    }

    public function testLastLogin()
    {
        $this->user->setLastLogin("2025");
        $this->assertEquals("2025", $this->user->getLastLogin());
    }
}
