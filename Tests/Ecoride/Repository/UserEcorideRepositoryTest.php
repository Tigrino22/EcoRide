<?php

namespace Ecoride\Repository;

use PHPUnit\Framework\TestCase;
use Tigrino\App\Profile\Entity\UserEcoride;
use Tigrino\App\Profile\Repository\UserEcorideRepository;
use Tigrino\Core\Database\Database;

class UserEcorideRepositoryTest extends TestCase
{
    private UserEcorideRepository $repository;
    private Database $db;
    private UserEcoride $user;

    protected function setUp(): void
    {
        $this->db = new Database('sqlite');
        $this->repository = new UserEcorideRepository($this->db);

        $this->db->execute('DROP TABLE IF EXISTS users_roles');
        $this->db->execute('DROP TABLE IF EXISTS roles');
        $this->db->execute('DROP TABLE IF EXISTS users');

        $this->db->execute("CREATE TABLE IF NOT EXISTS users (
            id BLOB PRIMARY KEY not null,
            username TEXT not null,
            name TEXT not null,
            firstname TEXT not null,
            email TEXT not null,
            password TEXT not null,
            telephone TEXT,
            address TEXT,
            birthday TEXT,
            photo BLOB,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP
        )");

        $this->db->execute('CREATE TABLE IF NOT EXISTS roles (
            id BLOB PRIMARY KEY,
            name TEXT,
            number INTEGER
        )');

        $this->db->execute('CREATE TABLE IF NOT EXISTS users_roles (
            user_id BLOB,
            role_id BLOB,
            PRIMARY KEY (user_id, role_id)
        )');

        $this->db->execute(
            'INSERT INTO roles (id, name, number)
            VALUES 
                (?, "SUPERADMIN", 0),
                (?, "ADMIN", 1),
                (?, "USER", 2),
                (?, "GUEST", 3)',
            [
                hex2bin('08cc137eba2a42078f7202c7f859fea2'),  // Conversion en binaire
                hex2bin('284c4c6acb3349a2abb2bfa4083a59b2'),  // Conversion en binaire
                hex2bin('3bb93f51b0834fa9bd4b55e358b62e1c'),  // Conversion en binaire
                hex2bin('05f10bf37bec45128ae2d236b5786eab')   // Conversion en binaire
            ]
        );

        $this->user = new UserEcoride([
            "username" => "Tigrino",
            "password" => "password",
            "email" => "email@email.com",
            "name" => "test",
            "firstname" => "firstTest",
            "lastLogin" => "lastLogin",
            "created_at" => date('Y-m-d H:i:s'),
            "updated_at" => date('Y-m-d H:i:s'),
            "address" => "address",
            "birthday" => "birthday",
            "telephone" => "telephone"
        ]);
    }

    public function testCreateUser()
    {
        $userCreated = $this->repository->insert($this->user);

        $this->assertNotNull($userCreated);
        $this->assertInstanceOf(UserEcoride::class, $userCreated);
    }

    public function testUpdateUser()
    {
        $userUpdated = $this->repository->insert($this->user);

        $userUpdated->setUsername("UpdatedUsername");
        $userUpdated->setBirthday("UpdatedBirthday");
        $this->repository->update($userUpdated);
        $userUpdated = $this->repository->findByUsername('UpdatedUsername');

        $this->assertNotNull($userUpdated);
        $this->assertInstanceOf(UserEcoride::class, $userUpdated);
        $this->assertEquals("UpdatedUsername", $userUpdated->getUsername());
        $this->assertEquals("UpdatedBirthday", $userUpdated->getBirthday());
    }
}
