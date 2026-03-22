<?php
require_once 'config.php';
require_once 'db.php';

echo "=== Configuration de la base de données ===\n\n";

try {
    // Créer les tables si elles n'existent pas
    $sql = file_get_contents('database.sql');
    $statements = array_filter(array_map('trim', explode(';', $sql)));

    foreach ($statements as $statement) {
        if (!empty($statement) && !preg_match('/^(CREATE DATABASE|USE)/i', $statement)) {
            try {
                $db->exec($statement);
            } catch (Exception $e) {
                // Ignorer les erreurs de table déjà existante
                if (!str_contains($e->getMessage(), 'already exists')) {
                    echo "Erreur SQL : " . $e->getMessage() . "\n";
                }
            }
        }
    }

    echo "Tables créées/vérifiées.\n\n";

    // Vérifier et corriger les utilisateurs existants
    $stmt = $db->query("SELECT id, login, password, role FROM utilisateurs");
    $existingUsers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo "Utilisateurs existants :\n";
    foreach ($existingUsers as $user) {
        echo "- {$user['login']} ({$user['role']})\n";
    }

    if (count($existingUsers) == 0) {
        echo "\nAucun utilisateur trouvé. Création d'utilisateurs de test...\n";

        // Insérer des utilisateurs de test avec mots de passe hashés
        $users = [
            [
                'nom' => 'Admin',
                'prenom' => 'System',
                'login' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role' => 'administrateur'
            ],
            [
                'nom' => 'Editeur',
                'prenom' => 'Test',
                'login' => 'editeur',
                'password' => password_hash('editeur123', PASSWORD_DEFAULT),
                'role' => 'editeur'
            ]
        ];

        $stmt = $db->prepare("INSERT INTO utilisateurs (nom, prenom, login, password, role) VALUES (?, ?, ?, ?, ?)");

        foreach ($users as $user) {
            $stmt->execute([$user['nom'], $user['prenom'], $user['login'], $user['password'], $user['role']]);
            echo "✓ Utilisateur {$user['login']} créé\n";
        }
    }

    echo "\n=== Identifiants de connexion ===\n";
    echo "Administrateur : login='admin', password='admin123'\n";
    echo "Éditeur : login='editeur', password='editeur123'\n";

} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
?>