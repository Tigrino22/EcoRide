<?php

namespace Tigrino\App\Profile\Services;

use DateTime;

class CarValidator
{
    /**
    * Valide les données d'une voiture.
    *
    * @param array $car
    * @return array Les données validées ou un tableau d'erreurs
    */
    public static function validate(array $car): array
    {
        $errors = [];

        if (empty($car['model'])) {
            $errors['model'] = 'Le modèle est requis.';
        }
        if (empty($car['brand'])) {
            $errors['brand'] = 'La marque est requise.';
        }
        if (empty($car['color'])) {
            $errors['color'] = 'La couleur est requise.';
        }
        if (empty($car['plateOfRegistration'])) {
            $errors['plateOfRegistration'] = 'La plaque d\'immatriculation est requise.';
        }
        if (empty($car['firstRegistrationAt'])) {
            $errors['firstRegistrationAt'] = 'La date de première immatriculation est requise.';
        } else {
            $date = \DateTime::createFromFormat('Y-m-d', $car['firstRegistrationAt']);
            if ($date === false || $date->format('Y-m-d') !== $car['firstRegistrationAt']) {
                $errors['firstRegistrationAt'] = 'La date de première immatriculation est invalide.';
            } else {
                $car['firstRegistrationAt'] = $date->format('Y-m-d');
            }
        }

        $car['places'] = (int) $car['places'];
        if ($car['places'] <= 0) {
            $errors['places'] = 'Le nombre de places doit être un entier positif.';
        }

        if (!empty($errors)) {
            return ['errors' => $errors];
        }

        return ['data' => $car];
    }
}
