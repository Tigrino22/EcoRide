<?php

if ($argc !== 3) {
    echo "Usage: php add-module.php <module_entry> <module_name>\n";
    exit(1);
}

$moduleEntry = $argv[1];
$moduleName = $argv[2];
$configFile = './Config/Modules.php';
$useEntry = "use Tigrino\\$moduleName\\{$moduleName}Module;";

// Vérifie que le fichier de configuration existe
if (!file_exists($configFile)) {
    echo "Le fichier $configFile n'existe pas.\n";
    exit(1);
}

// Lire le contenu du fichier
$content = file_get_contents($configFile);

// Ajouter l'entrée 'use' si elle n'existe pas
if (strpos($content, $useEntry) === false) {
    // Ajoute la nouvelle entrée 'use' avant la déclaration 'return ['
    $content = preg_replace('/^return\s*\[/m', "$useEntry\n\nreturn [", $content);
}

// Ajouter l'entrée du module à la liste des modules si elle n'existe pas
if (strpos($content, $moduleEntry) === false) {
    $content = preg_replace('/^\s*return \[/m', "return [\n    $moduleEntry,", $content);
}

// Écrire les modifications dans le fichier
file_put_contents($configFile, $content);

echo "Le module '$moduleName' a été ajouté à '$configFile'.\n";
