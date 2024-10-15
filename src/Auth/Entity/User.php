<?php

namespace Tigrino\Auth\Entity;

use Tigrino\Auth\Config\Role;

/**
 * Class UserRepository de base
 * Cette classe représente l'entité UserRepository qui est hydratée
 * avec des données récupérées depuis la base de données.
 *
 * Il est important de préciser que le role par défaut d'un
 * utilisateur inscrit est USER = 2
 */
class User extends AbstractUser
{
    protected ?string $email;
    protected ?string $lastLogin;
    private array $roles;


    public function __construct(
        string $username,
        string $password,
        ?string $email = null,
        array $roles = [Role::USER],
        ?string $lastLogin = null
    ) {
        parent::__construct($username, $password);
        $this->roles = $roles;
        $this->email = $email;
        $this->lastLogin = $lastLogin;
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

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }
}
