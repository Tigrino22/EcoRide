<?php

namespace Tigrino\Auth\Repository;

use Ramsey\Uuid\Uuid;
use Tigrino\Auth\Entity\User;
use Tigrino\Core\Database\Database;

class UserRepository
{
    public $id;
    public $username;
    public $email;

    private Database $db;

    public function __construct()
    {
        $this->db = new Database(); // Connexion via la classe Database
    }

    public function insert(User $user): bool
    {
            // Insertion d'un nouvel utilisateur
            $query = 'INSERT INTO users 
                (id, username, email, password, roles) 
                VALUES (:id, :username, :email, :password, :roles)';
            $params = [
                ':id' => $user->getUuid(),
                ':username' => $user->getUsername(),
                ':email' => $user->getEmail(),
                ':password' => $user->getPassword(),
                ':roles' => json_encode($user->getRoles())
            ];
            return $this->db->execute($query, $params);
    }

    public function update(User $user): bool
    {
            // Mise à jour de l'utilisateur
            $query = 'UPDATE users SET 
                username = :username,
                email = :email,
                roles = :roles,
                session_token = :session_token,
                last_login = :last_login
                WHERE id = :id';
            $params = [
                ':id' => $user->getUuid(),
                ':username' => $user->getUsername(),
                ':email' => $user->getEmail(),
                ':roles' => json_encode($user->getRoles()),
                ':session_token' => $user->getSessionToken(),
                ':last_login' => $user->getLastLogin()
            ];
            return $this->db->execute($query, $params);
    }

    public function findByEmail(string $email): ?User
    {
        $query = 'SELECT * FROM users WHERE email = :email LIMIT 1';
        $params = [':email' => $email];

        $result = $this->db->query($query, $params);
        if ($result) {
            return self::mapDataToUser($result[0]);
        }

        return null;
    }

    public function findByUsername(string $username): ?User
    {
        $query = 'SELECT * FROM users WHERE username = :username LIMIT 1';
        $params = [':username' => $username];

        $result = $this->db->query($query, $params);
        if ($result) {
            return self::mapDataToUser($result[0]);
        }

        return null;
    }

    public function findBySessionToken(string $token): ?User
    {
        $query = 'SELECT * FROM users WHERE session_token = :token LIMIT 1';
        $params = [':token' => $token];

        $result = $this->db->query($query, $params);
        if ($result) {
            return self::mapDataToUser($result[0]);
        }

        return null;
    }

    /**
     * Cette fonction retourne un utilisateur
     * objet si une correspondance est
     * trouvée avec les méthodes find.
     *
     * @param array $data
     * @return User
     */
    private static function mapDataToUser(array $data): User
    {
        return new User(
            id: $data['id'],
            username: $data['username'],
            password: $data['password'],
            roles: json_decode($data['roles'], true),  // on decode les roles en tableau
            email: $data['email'],
            sessionToken: $data['session_token'],
            lastLogin: $data['last_login']
        );
    }
}
