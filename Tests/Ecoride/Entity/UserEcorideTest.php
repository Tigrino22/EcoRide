<?php

namespace Ecoride\Entity;

use PHPUnit\Framework\TestCase;
use Tigrino\App\Profile\Entity\UserEcoride;

class UserEcorideTest extends TestCase
{
    private UserEcoride $entity;

    protected function setUp(): void
    {
        $this->entity = new UserEcoride([
            "username" => "Tigrino",
            "password" => "password",
            "email" => "email@email.com",
            "name" => "test",
            "firstname" => "firstTest",
            "lastLogin" => "lastLogin",
            "created_at" => "createdAt",
            "updated_at" => "updatedAt",
            "address" => "address",
            "birthday" => "birthday",
            "telephone" => "telephone"
        ]);
    }

    public function testGetAndSetUsername()
    {
        $this->assertEquals('Tigrino', $this->entity->getUsername());
        $this->entity->setUsername("username1");
        $this->assertEquals('username1', $this->entity->getUsername());
    }

    public function testGetAndSetPassword()
    {
        $this->assertTrue(password_verify("password", $this->entity->getPassword()));
        $this->entity->setPassword("password1");
        $this->assertTrue(password_verify("password1", $this->entity->getPassword()));
    }

    public function testGetAndSetEmail()
    {
        $this->assertEquals('email@email.com', $this->entity->getEmail());
        $this->entity->setEmail("email@email.com1");
        $this->assertEquals('email@email.com1', $this->entity->getEmail());
    }

    public function testGetAndSetName()
    {
        $this->assertEquals('test', $this->entity->getName());
        $this->entity->setName("name1");
        $this->assertEquals('name1', $this->entity->getName());
    }

    public function testGetAndSetFirstName()
    {
        $this->assertEquals('firstTest', $this->entity->getFirstName());
        $this->entity->setFirstName("firstName1");
        $this->assertEquals('firstName1', $this->entity->getFirstName());
    }

    public function testGetAndSetLastLogin()
    {
        $this->assertEquals('lastLogin', $this->entity->getLastLogin());
        $this->entity->setLastLogin("lastLogin1");
        $this->assertEquals('lastLogin1', $this->entity->getLastLogin());
    }

    public function testGetAndSetCreatedAt()
    {
        $this->assertEquals('createdAt', $this->entity->getCreatedAt());
        $this->entity->setCreatedAt("createdAt1");
        $this->assertEquals('createdAt1', $this->entity->getCreatedAt());
    }

    public function testGetAndSetUpdatedAt()
    {
        $this->assertEquals('updatedAt', $this->entity->getUpdatedAt());
        $this->entity->setUpdatedAt("updatedAt1");
        $this->assertEquals('updatedAt1', $this->entity->getUpdatedAt());
    }

    public function testGetAndSetPhone()
    {
        $this->assertEquals('telephone', $this->entity->getTelephone());
        $this->entity->setTelephone("telephone1");
        $this->assertEquals('telephone1', $this->entity->getTelephone());
    }

    public function testGetAndSetAddress()
    {
        $this->assertEquals('address', $this->entity->getAddress());
        $this->entity->setAddress("address1");
        $this->assertEquals('address1', $this->entity->getAddress());
    }

    public function testGetAndSetBirthday()
    {
        $this->assertEquals('birthday', $this->entity->getBirthday());
        $this->entity->setBirthday("birthday1");
        $this->assertEquals('birthday1', $this->entity->getBirthday());
    }
}
