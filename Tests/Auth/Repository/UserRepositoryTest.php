<?php

namespace Tests\Auth\Repository;

use Dotenv\Dotenv;
use PHPUnit\Framework\TestCase;
use Tigrino\Auth\Config\Role;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Database\Database;

class UserRepositoryTest extends TestCase
{
    private Database $db;
    private UserRepository $repository;


    protected function setUp(): void
    {
        $this->db = new Database('sqlite');
        $this->repository = new UserRepository($this->db);

        $this->db->execute('DROP TABLE IF EXISTS users_roles');
        $this->db->execute('DROP TABLE IF EXISTS roles');
        $this->db->execute('DROP TABLE IF EXISTS users');

        $this->db->execute('CREATE TABLE IF NOT EXISTS users (
            id BLOB PRIMARY KEY,
            username TEXT,
            email TEXT,
            password TEXT,
            last_login DATETIME
        )');

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

        $user = new User('test_user', 'password123', 'test@example.com', [Role::ADMIN]);
        $user2 = new User('test_user2', 'password123', 'test2@example.com');
        $user3 = new User('GUEST', 'password123', 'test3@example.com');
        $this->repository->insert($user);
        $this->repository->insert($user2);
        $this->repository->insert($user3);
    }

    public function testInsertUser()
    {
        $user = new User('test_user3', 'password123', 'test3@example.com');

        $result = $this->repository->insert($user);
        $this->assertTrue((bool)$result);
    }

    public function testUpdateUser()
    {
        $user = new User('test_user3', 'password123', 'test3@example.com');
        $this->repository->insert($user);
        $user->setUsername('test_updated');
        $result = $this->repository->update($user);
        $this->assertTrue((bool)$result);
        $this->assertEquals('test_updated', $result->getUsername());
        $this->assertInstanceOf(User::class, $result);
    }

    public function testDeleteUser()
    {
        $user = new User('test_user3', 'password123', 'test3@example.com');

        $this->repository->insert($user);
        $this->assertTrue($this->repository->delete($user));

        $this->repository->insert($user);
        $this->assertTrue($this->repository->delete($user->getId()));

        $this->repository->insert($user);
    }

    public function testFindByUsername()
    {
        $user = $this->repository->findByUsername('test_user');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test_user', $user->getUsername());

        $user_wrong = $this->repository->findByUsername('wrong_user');

        $this->assertNull($user_wrong);
    }

    public function testFindByEmail()
    {
        $user = $this->repository->findByEmail('test@example.com');

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals('test_user', $user->getUsername());

        $user_wrong = $this->repository->findByUsername('wrong@mail.fr');

        $this->assertNull($user_wrong);
    }

    public function testUpdateAndFindAgainUser()
    {
        $user = $this->repository->findByUsername('test_user');
        $this->assertNotNull($user, "L'utilisateur 'test_user' n'a pas été trouvé.");

        $user->setUsername('test_user_modified');

        $result = $this->repository->update($user);
        $this->assertInstanceOf(User::class, $result);

        $updatedUser = $this->repository->findByUsername('test_user_modified');
        $this->assertNotNull(
            $updatedUser,
            "L'utilisateur 'test_user_modified' n'a pas été trouvé après la mise à jour."
        );

        $this->assertEquals('test_user_modified', $updatedUser->getUsername());
    }

    public function testGetRoleUser()
    {
        $user = $this->repository->findByUsername('test_user2');
        $role = $this->repository->getRoles($user);

        $this->assertIsArray($role);
        $this->assertContains(Role::USER, $role);
    }

    /**
     * @throws \Exception
     */
    public function testSetRoleUser()
    {
        $user = $this->repository->findByUsername('test_user');

        $this->assertInstanceOf(User::class, $user);

        $user_result = $this->repository->setRole($user, [Role::USER]);
        $this->assertInstanceOf(User::class, $user_result);

        $this->assertContains(Role::USER, $user_result->getRoles());

        $user = $this->repository->findByUsername('GUEST');
        $this->assertInstanceOf(User::class, $user);
        $role = $this->repository->getRoles($user);

        $this->assertIsArray($role);
        $this->assertContains(Role::GUEST, $role);
    }

    /**
     * @throws \Exception
     */
    public function testSetRoleDoesNotExist()
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Aucun rôle n'a été trouvé avec le code : 5");

        $user = $this->repository->findByUsername('test_user');
        $this->repository->setRole($user, [5]);
    }
}
