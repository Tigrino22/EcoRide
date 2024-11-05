<?php

namespace Tigrino\App\Profile\Repository;

use Ramsey\Uuid\Uuid;
use Tigrino\App\Profile\Entity\UserEcoride;
use Tigrino\Auth\Entity\User;
use Tigrino\Auth\Repository\UserRepository;
use Tigrino\Core\Database\Database;
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
             telephone, 
             address, 
             birthday, 
             photo, 
             is_passenger,
             is_driver,
             created_at, 
             updated_at) 
            VALUES (:id, 
                    :username, 
                    :name, 
                    :firstname, 
                    :email, 
                    :password, 
                    :telephone, 
                    :address, 
                    :birthday, 
                    :photo,
                    :is_passenger,
                    :is_driver,
                    :created_at, 
                    :updated_at)';

        $params = [
            ':id' => $user->getId()->getBytes(), // UUID en format binaire
            ':username' => $user->getUsername(),
            ':name' => $user->getName(),
            ':firstname' => $user->getFirstname(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':telephone' => $user->getTelephone(),
            ':address' => $user->getAddress(),
            ':birthday' => $user->getBirthday(),
            ':photo' => $user->getPhoto(), // Attention au format de l'insertion ici
            ':is_passenger' => $user->getIsPassenger(),
            ':is_driver' => $user->getIsDriver(),
            ':created_at' => $user->getCreatedAt() ?: date('Y-m-d H:i:s'),
            ':updated_at' => $user->getUpdatedAt() ?: date('Y-m-d H:i:s')
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
            telephone = :telephone,
            address = :address,
            birthday = :birthday,
            photo = :photo,
            is_passenger = :is_passenger,
            is_driver = :is_driver,
            updated_at = :updated_at
            WHERE id = :id';

        $params = [
            ':id' => $user->getId()->getBytes(),
            ':username' => $user->getUsername(),
            ':name' => $user->getName(),
            ':firstname' => $user->getFirstname(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
            ':telephone' => $user->getTelephone(),
            ':address' => $user->getAddress(),
            ':birthday' => $user->getBirthday(),
            ':photo' => $user->getPhoto(), // gérer le format du BLOB pour la photo
            ':is_passenger' => $user->getIsPassenger(),
            ':is_driver' => $user->getIsDriver(),
            ':updated_at' => date('Y-m-d H:i:s') // Met à jour avec la date et l'heure actuelles
        ];

        return $this->flush($user, $query, $params);
    }

    /**
     * Cette fonction retourne un objet UserEcoride
     * si une correspondance est trouvée avec les méthodes find.
     *
     * @param array $data
     * @return UserEcoride
     */
    protected function mapDataToUser(array $data): UserEcoride
    {
        $user = new UserEcoride($data);

        $user->setId(Uuid::fromBytes($data['id']));
        $user->setRoles($this->getRoles($user));

        return $user;
    }
}
