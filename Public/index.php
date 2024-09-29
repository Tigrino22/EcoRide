<?php

require "../vendor/autoload.php";
require dirname(__DIR__) . DIRECTORY_SEPARATOR . "Config" . DIRECTORY_SEPARATOR . "Config.php";

use Config\Config;
use Tigrino\Core\App;
use function Http\Response\send;
use GuzzleHttp\Psr7\ServerRequest;
use Tigrino\Core\Middleware\WhoopsMiddleware;

// Chagrement des configuration
Config::load();

// import des modules depuis le fichiers de configuration Config/Modules.php
$middlewares = Config::CONFIG_DIR . "/Middlewares.php";
$modules = Config::CONFIG_DIR . "/Modules.php";

// Initialisation de l'app en passant le tableau de routes en paramètre.
$app = new App(include($modules));

// Mise en place de Whoops pour l'affiche-age des erreur
// en environnement de développement.
if (getenv("APP_ENV") === "DEV") {
    $app->addMiddleware(new WhoopsMiddleware());
}

$app->addMiddleware(include($middlewares));

$response = $app->run(ServerRequest::fromGlobals());

send($response);
