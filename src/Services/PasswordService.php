<?php

namespace Tigrino\Services;

class PasswordService
{
    /**
     * @param string $password
     * @return array|bool
     */
    public static function passwordValidator(string $password)
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Le mot de passe doit contenir au moins 8 caractères';
        }

        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une lettre majuscule';
        }

        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins une lettre minuscule';
        }

        if (!preg_match('/\d/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un chiffre';
        }

        if (!preg_match('/[@$!%*?&#]/', $password)) {
            $errors[] = 'Le mot de passe doit contenir au moins un caractère spécial (ex: @, #, $)';
        }

        return empty($errors) ? true : $errors;
    }
}
