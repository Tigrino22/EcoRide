<?php

namespace Tests\Auth\Entity;

use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Tigrino\Auth\Config\Role;
use Tigrino\Auth\Entity\User;

class UserTest extends TestCase
{
    private User $user;

    protected function setUp(): void
    {
        $this->user = new User(
            'Tigrino',
            'test',
            'test@test.fr',
            [Role::USER],
            '01/01/1970'
        );
    }

    public function testGetAndSetId()
    {
        $uuid = $this->user->getUuid();

        $this->assertInstanceOf(UuidInterface::class, $uuid);

        $new_uuid = Uuid::uuid4();
        $this->user->setUuid($new_uuid);

        $this->assertEquals($new_uuid, $this->user->getUuid());
    }

    public function testSetPassword()
    {
        $this->user->setPassword('test2');

        $this->assertTrue(password_verify('test2', $this->user->getPassword()));
    }

    public function testSetEmail()
    {
        $this->user->setEmail('test2@test.fr');
        $this->assertEquals('test2@test.fr', $this->user->getEmail());
    }

    public function testSetLastLogin()
    {
        $this->user->setLastLogin('01/01/2000');
        $this->assertEquals('01/01/2000', $this->user->getLastLogin());
    }
}
