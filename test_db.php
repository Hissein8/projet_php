<?php
require_once 'config.php';
require_once 'db.php';

echo "=== Test de connexion à la base de données ===\n\n";

try {
    echo "Connexion réussie à la base de données '" . DB_NAME . "'\n\n";

    // Vérifier les tables
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
    echo "Tables trouvées :\n";
    foreach ($tables as $table) {
        echo "- $table\n";
    }
    echo "\n";

    // Vérifier la table utilisateurs
    if (in_array('utilisateurs', $tables)) {
        echo "=== Contenu de la table 'utilisateurs' ===\n";
        $users = $db->query("SELECT id, nom, prenom, login, role, date_creation FROM utilisateurs")->fetchAll(PDO::FETCH_ASSOC);

        if (count($users) > 0) {
            foreach ($users as $user) {
                echo "ID: {$user['id']}, Nom: {$user['nom']}, Login: {$user['login']}, Rôle: {$user['role']}\n";
            }
        } else {
            echo "Aucun utilisateur trouvé dans la table.\n";
        }
    } else {
        echo "La table 'utilisateurs' n'existe pas !\n";
    }

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>