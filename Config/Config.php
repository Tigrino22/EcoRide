<?php

/**
 * Fichier de configuration et d'initialisation.
 * Déclaration des constantes et initialisation des paramètres.
 */

 namespace Config;

use DI\Container;
use DI\ContainerBuilder;
use DI\DependencyException;
use Dotenv\Dotenv;
use Exception;
use Tigrino\Core\Errors\ErrorHandler;

class Config
{
    public const BASE_PATH = __DIR__ . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR;
    public const CONFIG_DIR = __DIR__ . DIRECTORY_SEPARATOR;
    private static Container $container;

    public static function load()
    {
        // Chargement des variables d'environnement
        $dotenv = Dotenv::createUnsafeImmutable(self::BASE_PATH);
        $dotenv->load();

        // Enregistrement du ErrorHandler pour la capture des erreurs
        $errorHandler = new ErrorHandler();
        $errorHandler->register();

        self::setContainer();
    }

    public static function getContainer(): Container
    {
        return self::$container;
    }

    private static function setContainer(): void
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(__DIR__ . DIRECTORY_SEPARATOR . 'Container.php');
        try {
            self::$container = $containerBuilder->build();
        } catch (Exception $e) {
            throw new DependencyException("Container wasn't initialized");
        }
    }
}
