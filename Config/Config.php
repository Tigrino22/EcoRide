<?php

/**
 * Fichier de configuration et d'initialisation.
 * Déclaration des constantes et initialisation des paramètres.
 */

 namespace Config;

use Dotenv\Dotenv;
use Tigrino\Core\Errors\ErrorHandler;

class Config
{
    public const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
    public const CONFIG_DIR = __DIR__ . DIRECTORY_SEPARATOR;

    public static function load()
    {
        // Chargement des variables d'environnement
        $dotenv = Dotenv::createUnsafeImmutable(self::BASE_PATH);
        $dotenv->load();

        // Enregistrement du ErrorHandler pour la capture des erreurs
        $errorHandler = new ErrorHandler();
        $errorHandler->register();
    }
}
