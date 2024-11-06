<?php

namespace Tigrino\App\Profile\Services;

use DateTime;

class UserValidator
{
    public static function validate(array $data): array
    {
        $errors = [];

        if (empty($data['name'])) {
            $errors['name'] = 'Le nom est requis.';
        }
        if (empty($data['firstname'])) {
            $errors['firstname'] = 'Le prénom est requis.';
        }
        if (empty($data['email'])) {
            $errors['email'] = 'L’email est requis.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'L’email est invalide.';
        }

        if (!empty($data['birthday'])) {
            $date = DateTime::createFromFormat('Y-m-d', $data['birthday']);
            if ($date === false || $date->format('Y-m-d') !== $data['birthday']) {
                $errors['birthday'] = 'La date de naissance est invalide.';
            } else {
                $data['birthday'] = $date->format('Y-m-d');
            }
        }

        if (!empty($data['telephone']) && !preg_match('/^[0-9\s\-\+\(\)]+$/', $data['telephone'])) {
            $errors['telephone'] = 'Le numéro de téléphone est invalide.';
        }

        $data['is_driver'] =
            isset($data['is_driver']) ? filter_var($data['is_driver'], FILTER_VALIDATE_BOOLEAN) : false;
        $data['is_passenger'] =
            isset($data['is_passenger']) ? filter_var($data['is_passenger'], FILTER_VALIDATE_BOOLEAN) : true;


        return !empty($errors) ? ['errors' => $errors] : $data;
    }
}
