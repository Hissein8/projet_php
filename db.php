<?php
try {
    $db = new PDO("mysql:host=".$_ENV['dbhost'].";dbname=".$_ENV['dbname'].";charset=utf8mb4", $_ENV['dbuser'], $_ENV['dbpass']);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    error_log($e->getMessage());
    die("Erreur de connexion à la base de données.");
}