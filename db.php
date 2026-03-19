<?php
// Charger les variables d'environnement depuis le fichier .env
if (file_exists(__DIR__ . '/.env')) {
    $envFile = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($envFile as $line) {
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            // Supprimer les guillemets si présents
            $value = trim($value, '"\'');
            $_ENV[$key] = $value;
        }
    }
}

// Valeurs par défaut si les variables d'environnement ne sont pas définies
$dbhost = $_ENV['dbhost'] ?? 'localhost';
$dbname = $_ENV['dbname'] ?? 'actualites';
$dbuser = $_ENV['dbuser'] ?? 'root';
$dbpass = $_ENV['dbpass'] ?? '';

try {
    $db = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4", $dbuser, $dbpass);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Erreur de connexion à la base de données.");
}