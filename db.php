<?php
require_once __DIR__ . '/config.php';

// detection automatique du nom du dossier racine (ex: /projet_php) pour BASE_URL
// define('BASE_URL', '/' . basename(__DIR__));

// Chemin serveur absolu (pour move_uploaded_file)
define('BASE_PATH', dirname(__DIR__)); 
// // pointe vers projet_php/

// URL publique (pour afficher les images dans le navigateur)
define('BASE_URL', '/projet_php'); 
// ou détection automatique :
// define('BASE_URL', rtrim(dirname($_SERVER['SCRIPT_NAME']), '/articles'));

try {
    $db = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS
    );
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Erreur de connexion à la base de données.");
}
