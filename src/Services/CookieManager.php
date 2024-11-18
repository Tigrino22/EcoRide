<?php

namespace Tigrino\Services;

class CookieManager
{
    /**
    * Définit un cookie.
    *
    * @param string $name
    * @param string $value
    * @param int $expires (en secondes)
    * @param bool $httpOnly
    * @param bool $secure
    * @return void
    */
    public static function set(
        string $name,
        string $value,
        int $expires = 3600, // Durée de 1 heure par défaut
        bool $httpOnly = false,
        bool $secure = false
    ): void {
        setcookie(
            $name,
            $value,
            [
            'expires' => time() + $expires,
            'path' => '/',
            'domain' => '', // Optionnel : configurer votre domaine
            'secure' => $secure,
            'httponly' => $httpOnly,
            'samesite' => 'Strict'
            ]
        );
    }

    /**
    * Récupère la valeur d'un cookie.
    *
    * @param string $name
    * @return string|null
    */
    public static function get(string $name): ?string
    {
        return $_COOKIE[$name] ?? null;
    }

    /**
    * Supprime un cookie.
    *
    * @param string $name
    * @return void
    */
    public static function delete(string $name): void
    {
        setcookie($name, '', time() - 3600, '/');
        unset($_COOKIE[$name]);
    }
}
