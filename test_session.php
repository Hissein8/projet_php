<?php
require_once 'entete.php';

echo "<h2>Test de session</h2>";

if (isset($_SESSION['user'])) {
    echo "<div style='background: #d4edda; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>✅ Session active</h3>";
    echo "<p><strong>Utilisateur :</strong> " . htmlspecialchars($_SESSION['user']['prenom'] . ' ' . $_SESSION['user']['nom']) . "</p>";
    echo "<p><strong>Login :</strong> " . htmlspecialchars($_SESSION['user']['login']) . "</p>";
    echo "<p><strong>Rôle :</strong> " . htmlspecialchars($_SESSION['user']['role']) . "</p>";
    echo "<p><strong>ID :</strong> " . htmlspecialchars($_SESSION['user']['id']) . "</p>";
    echo "</div>";

    echo "<p><a href='deconnexion.php' style='color: #dc3545;'>Se déconnecter</a></p>";
} else {
    echo "<div style='background: #f8d7da; padding: 15px; border-radius: 8px; margin: 20px 0;'>";
    echo "<h3>❌ Aucune session active</h3>";
    echo "<p>Vous n'êtes pas connecté.</p>";
    echo "</div>";

    echo "<p><a href='connexion.php'>Se connecter</a></p>";
}

echo "<hr>";
echo "<h3>Contenu de \$_SESSION :</h3>";
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

require_once 'pied.php';
?>