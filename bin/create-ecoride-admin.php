#!/usr/bin/env php
<?php

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . "/../src/Core/Misc/utils.php";

use Tigrino\App\Profile\Entity\UserEcoride;
use Tigrino\App\Profile\Repository\UserEcorideRepository;
use Tigrino\Auth\Config\Role;
use Tigrino\Core\Database\Database;

Dotenv\Dotenv::createUnsafeImmutable(dirname(__DIR__))->load();

// Fonction pour masquer l'entrée du mot de passe
function promptPassword(string $prompt): string
{
    echo $prompt;
    shell_exec('stty -echo'); // Cache l'entrée utilisateur
    $password = trim(fgets(STDIN));
    shell_exec('stty echo');  // Affiche à nouveau l'entrée utilisateur
    echo PHP_EOL;
    return $password;
}

$database = new Database();

colorLog("Création d'un nouvel administrateur\n", "i");

$username = readline('Entrez le nom d\'utilisateur : ');
$email = readline('Entrez l\'adresse email : ');
$firstname = readline('Entrez le prénom : ');
$name = readline('Entrez le nom : ');

while (true) {
    $password = promptPassword('Entrez le mot de passe : ');
    $password_confirm = promptPassword('Confirmez le mot de passe : ');

    if ($password === $password_confirm) {
        break;
    }
    colorLog("Erreur lors de la confirmation du mot de passe, veulliez recommencer.\n");
}

$password = password_hash($password, PASSWORD_DEFAULT);

$user = new UserEcoride([
    'username' => $username,
    'email' => $email,
    'firstname' => $firstname,
    'name' => $name,
    'password' => $password,
    'is_driver' => true
]);

try {
    $database->beginTransaction();
    $userRepository = new UserEcorideRepository();

    if ($userRepository->insert($user)) {
        if ($userRepository->setRole($user, [Role::ADMIN, Role::USER])) {
            $database->commit();
        } else {
            throw new Exception("Echec du setRole de l'administrateur.");
        }
        colorLog("Administrateur créé avec succès!\n", 's');
    } else {
        throw new \Exception("Échec de la sauvegarde de l'administrateur.");
    }
} catch (\Exception $e) {
    $database->rollback();
     colorLog($e->getMessage() . "\n", "e");
}
