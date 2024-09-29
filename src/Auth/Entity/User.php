<?php

namespace Tigrino\Auth\Entity;

/**
 * Class UserRepository de base
 * Cette classe représente l'entité UserRepository qui est hydratée
 * avec des données récupérées depuis la base de données.
 */
class User extends AbstractUser
{
    protected array $roles;
    protected ?string $email;
    protected ?string $sessionToken;
    protected ?string $lastLogin;


    public function __construct(
        $id,
        string $username,
        string $password,
        array $roles = [],
        ?string $email = null,
        ?string $sessionToken = null,
        ?string $lastLogin = null
    ) {
        parent::__construct($id, $username, $password);
        $this->roles = $roles;
        $this->email = $email;
        $this->sessionToken = $sessionToken;
        $this->lastLogin = $lastLogin;
    }

    /**
     * Vérifie si l'utilisateur a un rôle spécifique.
     *
     * @param string $role
     * @return bool
     */
    public function hasRole(string $role): bool
    {
        return in_array($role, $this->roles);
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @return string|null
     */
    public function getEmail(): ?string
    {
        return $this->email;
    }

    /**
     * @param string|null $email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getSessionToken(): ?string
    {
        return $this->sessionToken;
    }

    /**
     * @param string|null $sessionToken
     */
    public function setSessionToken(?string $sessionToken): void
    {
        $this->sessionToken = $sessionToken;
    }

    /**
     * @return string|null
     */
    public function getLastLogin(): ?string
    {
        return $this->lastLogin;
    }

    /**
     * @param string|null $lastLogin
     */
    public function setLastLogin(?string $lastLogin): void
    {
        $this->lastLogin = $lastLogin;
    }

    public function setUserCookie($token)
    {
        return setcookie(
            "session_token",
            $token, // le token de session généré
            [
                'expires' => time() + 3600,  // expiration dans 1 heure
                'path' => '/',
                'domain' => '', // votre domaine
                'secure' => true,  // true pour HTTPS uniquement
                'httponly' => true, // inaccessible via JavaScript
                'samesite' => 'Strict'  // ou 'Lax'
            ]
        );
    }
}
