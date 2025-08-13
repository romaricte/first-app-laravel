<?php

require_once 'vendor/autoload.php';

use OpenApi\Generator;

// Génération de la documentation Swagger
$openapi = Generator::scan(['app/']);

// Créer le dossier de stockage si nécessaire
$storageDir = 'storage/api-docs';
if (!is_dir($storageDir)) {
    mkdir($storageDir, 0755, true);
}

// Sauvegarder en JSON
file_put_contents($storageDir . '/api-docs.json', $openapi->toJson());

// Sauvegarder en YAML
file_put_contents($storageDir . '/api-docs.yaml', $openapi->toYaml());

echo "Documentation Swagger générée avec succès !\n";
echo "JSON: " . $storageDir . "/api-docs.json\n";
echo "YAML: " . $storageDir . "/api-docs.yaml\n";
