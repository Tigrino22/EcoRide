<?php

require "../vendor/autoload.php";
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "Config" . DIRECTORY_SEPARATOR . "Config.php";

use Config\Config;
use Tigrino\Core\App;
use Tigrino\Core\Middleware\WhoopsMiddleware;
use GuzzleHttp\Psr7\ServerRequest;
use function Http\Response\send;

// Chagrement des configuration
Config::load();

// Récupération du container DI
$container = Config::getContainer();

// import des modules depuis le fichiers de configuration Config/Modules.php
$middlewares = Config::CONFIG_DIR . "/Middlewares.php";
$modules = Config::CONFIG_DIR . "/Modules.php";

// Initialisation de l'app en passant le tableau de routes en paramètre.
$app = new App($container, include($modules));

// Mise en place de Whoops pour l'affiche-age des erreurs
// en environnement de développement.
if (getenv("APP_ENV") === "DEV") {
    $app->addMiddleware(new WhoopsMiddleware());
}

$app->addMiddleware(include($middlewares));

$response = $app->run(ServerRequest::fromGlobals());

send($response);
