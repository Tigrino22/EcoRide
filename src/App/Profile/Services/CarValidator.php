<?php

namespace Tigrino\App\Profile\Services;

use DateTime;
use Ramsey\Uuid\Uuid;
use Tigrino\App\Profile\Repository\CarRepository;

class CarValidator
{
    public static function validate(array $car): array
    {
        $errors = [];

        if (empty($car['model'])) {
            $errors['model'] = 'Le modèle est requis.';
        }
        if (empty($car['brand_id']) || !Uuid::isValid($car['brand_id'])) {
            $errors['brand_id'] = 'La marque est invalide ou manquante.';
        }
        if (empty($car['color'])) {
            $errors['color'] = 'La couleur est requise.';
        }
        if (empty($car['plate_of_registration'])) {
            $errors['plate_of_registration'] = 'La plaque d\'immatriculation est requise.';
        }
        if (empty($car['first_registration_at'])) {
            $errors['first_registration_at'] = 'La date de première immatriculation est requise.';
        } else {
            $date = DateTime::createFromFormat('Y-m-d', $car['first_registration_at']);
            if ($date === false || $date->format('Y-m-d') !== $car['first_registration_at']) {
                $errors['first_registration_at'] = 'La date de première immatriculation est invalide.';
            } else {
                $car['first_registration_at'] = $date->format('Y-m-d');
            }
        }

        $car['places'] = (int) $car['places'];
        if ($car['places'] <= 0) {
            $errors['places'] = 'Le nombre de places doit être un entier positif.';
        }

        if (empty($car['energie_id']) || !Uuid::isValid($car['energie_id'])) {
            $errors['energie_id'] = 'Le type d\'énergie est invalide ou manquant.';
        }

        return !empty($errors) ? ['errors' => $errors] : $car;
    }

    public static function plateAlreadyExists(string $plate): bool
    {
        $repository = new CarRepository();
        if ($repository->getCarByPlate($plate)) {
            return true;
        }
        return false;
    }
}
