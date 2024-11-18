<?php

namespace Tigrino\App\Profile\Repository;

use Ramsey\Uuid\Uuid;
use Tigrino\App\Profile\Entity\UserEcoride;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Database\Database;
use Tigrino\Core\Errors\ErrorHandler;
use Tigrino\Core\Misc\VarDumper;

class UserEcorideRepository extends UserRepository
{
    public function __construct(Database $database = null)
    {
        parent::__construct($database);
    }

    public function insert(User $user): ?User
    {
        // Insertion d'un nouvel utilisateur
        $query = 'INSERT INTO users 
            (id, 
             username, 
             name, 
             firstname, 
             email, 
             password, 
             phone, 
             address, 
             postal_code, 
             city, 
             birthday, 
             photo, 
             is_passenger, 
             is_driver, 
             created_at, 
             updated_at, 
             solde_id, 
             configuration_id) 
            VALUES (:id, 
                    :username, 
                    :name, 
                    :firstname, 
                    :email, 
                    :password, 
                    :phone, 
                    :address, 
                    :postal_code, 
                    :city, 
                    :birthday, 
                    :photo, 
                    :is_passenger, 
                    :is_driver, 
                    :created_at, 
                    :updated_at, 
                    :solde_id, 
                    :configuration_id)';

        $params = [
            ':id' => $user->getId()->getBytes(),
            ':username' => $user->getUsername(),
            ':name' => $user->getName(),
            ':firstname' => $user->getFirstname(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':phone' => $user->getPhone(),
            ':address' => $user->getAddress(),
            ':postal_code' => $user->getPostalCode(),
            ':city' => $user->getCity(),
            ':birthday' => $user->getBirthday(),
            ':photo' => $user->getPhoto(),
            ':is_passenger' => $user->getIsPassenger() ? 1 : 0,
            ':is_driver' => $user->getIsDriver() ? 1 : 0,
            ':created_at' => $user->getCreatedAt() ?: date('Y-m-d H:i:s'),
            ':updated_at' => $user->getUpdatedAt() ?: date('Y-m-d H:i:s'),
            ':solde_id' => $user->getSoldeId()?->getBytes(),
            ':configuration_id' => $user->getConfigurationId()?->getBytes()
        ];

        return $this->flush($user, $query, $params);
    }

    public function update(User $user): ?User
    {
        // Mise à jour de l'utilisateur
        $query = 'UPDATE users SET 
            username = :username,
            name = :name,
            firstname = :firstname,
            email = :email,
            password = :password,
            phone = :phone,
            address = :address,
            postal_code = :postal_code,
            city = :city,
            birthday = :birthday,
            photo = :photo,
            is_passenger = :is_passenger,
            is_driver = :is_driver,
            updated_at = :updated_at,
            solde_id = :solde_id,
            configuration_id = :configuration_id
            WHERE id = :id';

        $params = [
            ':id' => $user->getId()->getBytes(),
            ':username' => $user->getUsername(),
            ':name' => $user->getName(),
            ':firstname' => $user->getFirstname(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':phone' => $user->getPhone(),
            ':address' => $user->getAddress(),
            ':postal_code' => $user->getPostalCode(),
            ':city' => $user->getCity(),
            ':birthday' => $user->getBirthday(),
            ':photo' => $user->getPhoto(),
            ':is_passenger' => $user->getIsPassenger() ? 1 : 0,
            ':is_driver' => $user->getIsDriver() ? 1 : 0,
            ':updated_at' => date('Y-m-d H:i:s'),
            ':solde_id' => $user->getSoldeId()?->getBytes(),
            ':configuration_id' => $user->getConfigurationId()?->getBytes()
        ];

        return $this->flush($user, $query, $params);
    }

    public function findByStringId(string $id): array
    {
        $statement = 'SELECT * FROM users WHERE id = :id';
        $params = [
            ':id' => $id
        ];

        return $this->db->query($statement, $params);
    }

    public function toggleDriver(string $id): bool
    {
        $statement = 'UPDATE users SET is_driver = :is_driver WHERE id = :id';
        $params = [];

        $this->db->execute($statement, $params);
    }

    protected function mapDataToUser(array $data): UserEcoride
    {
        $user = new UserEcoride($data);
        $user->setId(Uuid::fromBytes($data['id']));
        $user->setRoles($this->getRoles($user));

        return $user;
    }
}
