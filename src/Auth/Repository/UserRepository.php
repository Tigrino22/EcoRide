<?php

namespace Tigrino\Auth\Repository;

use Exception;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use Tigrino\Auth\Config\Role;
use Tigrino\Auth\Entity\User;
use Tigrino\Core\Database\Database;
use Tigrino\Core\Errors\ErrorHandler;
use Tigrino\Core\Misc\VarDumper;

class UserRepository
{
    protected Database $db;

    public function __construct(Database $db = null)
    {
        $this->db = $db ?? new Database();
    }

    public function insert(User $user): ?User
    {
        // Insertion d'un nouvel utilisateur
        $query = 'INSERT INTO users 
            (id, username, email, password) 
            VALUES (:id, :username, :email, :password)';
        $params = [
            ':id' => $user->getId()->getBytes(),
            ':username' => $user->getUsername(),
            ':email' => $user->getEmail(),
            ':password' => $user->getPassword(),
        ];

        return $this->flush($user, $query, $params);
    }

    public function update(User $user): ?User
    {
        // Mise à jour de l'utilisateur
        $query = 'UPDATE users SET 
            username = :username,
            email = :email,
            last_login = :last_login
            WHERE id = :id';
        $params = [
            ':id' => $user->getId()->getBytes(),
            ':username' => $user->getUsername(),
            ':email' => $user->getEmail(),
            ':last_login' => $user->getLastLogin()
        ];

        return $this->flush($user, $query, $params);
    }

    public function delete(User|UuidInterface $param): bool
    {
        $query = 'DELETE FROM users WHERE id = :id';

        try {
            if ($param instanceof UuidInterface) {
                $params = [
                    ':id' => $param->getBytes(),
                ];
            } elseif ($param instanceof User) {
                $params = [
                    ':id' => $param->getId()->getBytes(),
                ];
            } else {
                throw new Exception(
                    'Parameter must be an instance of UuidInterface or User'
                );
            }

            return $this->db->execute($query, $params);
        } catch (Exception $e) {
            echo "Erreur lors de la suppression de l'utilisateur" . $e->getMessage();
            return false;
        }
    }

    public function findByEmail(string $email): ?User
    {
        $query = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        $params = [':email' => $email];

        $result = $this->db->query($query, $params);
        if ($result) {
            return $this->mapDataToUser($result[0]);
        }

        return null;
    }

    public function findByUsername(string $username): ?User
    {
        $query = 'SELECT * FROM users WHERE username = :username LIMIT 1';
        $params = [':username' => $username];

        $result = $this->db->query($query, $params);
        if ($result) {
            return $this->mapDataToUser($result[0]);
        }

        return null;
    }

    public function findById(UuidInterface $id): ?User
    {
        $query = 'SELECT * FROM users WHERE id = :id';
        $params = [':id' => $id->getBytes()];

        $result = $this->db->query($query, $params);
        if ($result) {
            return $this->mapDataToUser($result[0]);
        }

        return null;
    }

    /**
     * Modifie la liste des roles actuelle de
     * l'utilisateur vers une nouvelle liste
     *
     * @param User $user
     * @param array $roles
     * @return bool|User
     * @throws Exception
     */
    public function setRole(User $user, array $roles): bool|User
    {
        // Récupérer l'utilisateur
        $user_id = $user->getId()->getBytes();

        // Delete existing roles for the user to avoid duplicates
        $deleteQuery = '
                        DELETE FROM users_roles 
                        WHERE user_id = :user_id
                    ';
        $this->db->execute($deleteQuery, [':user_id' => $user_id]);

        // Prepare insert statement
        $insertQuery = '
                        INSERT INTO users_roles (user_id, role_id) 
                        VALUES (:user_id, :role_id)
                    ';

        // Tableau de int intermediaire pour user->setRole
        $array_to_set_user = [];

        try {
            $this->db->beginTransaction();

            foreach ($roles as $roleNumber) {
                // Get the role ID from the roles table based on the role number
                $roleResult = $this->db->query(
                    'SELECT id, number FROM roles WHERE number = :number',
                    [':number' => $roleNumber]
                );

                if (empty($roleResult)) {
                    throw new Exception("Aucun rôle n'a été trouvé avec le code : $roleNumber");
                }

                $roleId = $roleResult[0]['id'];

                $array_to_set_user[] = $roleResult[0]['number'];

                $params = [
                    ':user_id' => $user_id,
                    ':role_id' => $roleId
                ];

                $this->db->execute($insertQuery, $params);
            }

            $this->db->commit();

            $user->setRoles($array_to_set_user);

            return $user;
        } catch (Exception $e) {
            $this->db->rollback();
            throw $e;
        }
    }

    /**
     * Récupère via une requete SQL les roles de l'user
     * Et les retourne sous forme de tableau.
     *
     * @param User $user
     * @return array
     */
    public function getRoles(User $user): array
    {

        // Si l'utilisateur est un invité
        // on ne va pas plus loin
        if ($user->getUsername() === 'GUEST') {
            return [Role::GUEST];
        }

        $userId = $user->getId()->getBytes();

        // Récupération de tous les rôles de la table roles
        $query = "
                    SELECT r.id, r.name, r.number 
                    FROM roles r
                    JOIN users_roles ur ON ur.role_id = r.id
                    WHERE ur.user_id = :user_id
              ";

        $rolesData = $this->db->query($query, [':user_id' => $userId]);

        $roles = [];

        foreach ($rolesData as $role) {
            $roles[] = (int)$role['number'];
        }

        if (empty($roles)) {
            $roles[] = Role::GUEST;
        }

        return $roles;
    }

    /**
     * Cette fonction retourne un utilisateur
     * objet si une correspondance est
     * trouvée avec les méthodes find.
     *
     * @param array $data
     * @return User
     */
    protected function mapDataToUser(array $data): User
    {
        $user =  new User(
            username: $data['username'],
            password: $data['password'],
            email: $data['email'],
            lastLogin: $data['last_login']
        );

        $user->setId(Uuid::fromBytes($data['id']));
        $user->setRoles($this->getRoles($user));

        return $user;
    }

    /**
     * Enregistre les données d'un utilisateur en BDD
     *
     * @param User $user
     * @param string $query
     * @param array $params
     * @return false|User
     */
    protected function flush(User $user, string $query, array $params): false|User
    {
        try {
            // Enregistrement des rôles.
            $this->db->execute($query, $params);

            $this->setRole($user, $user->getRoles());

            return $user;
        } catch (Exception $exception) {
            ErrorHandler::logMessage(
                "Erreur lors de l'insertion ou de l'update de l'user : {$exception->getMessage()}"
            );

            return false;
        }
    }
}
