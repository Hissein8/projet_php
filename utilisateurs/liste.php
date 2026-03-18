<?php
require_once '../db.php';
//require_once '../entete.php';
require_once '../menu.php';

$sql = "SELECT * FROM utilisateurs";
$stmt = $db->prepare($sql);
$stmt->execute();
$utilisateurs = $stmt->fetchAll();

foreach ($utilisateurs as $user) {
    echo "ID: " . htmlspecialchars($user['id']) . " <a href='modifier.php?id=" . $user['id'] . "'>Modifier</a> <a href='supprimer.php?id=" . $user['id'] . "'>Supprimer</a><br>";
    echo "Nom: " . htmlspecialchars($user['nom']) . "<br>";
    echo "Prenom: " . htmlspecialchars($user['prenom']) . "<br>";
    echo "Login: " . htmlspecialchars($user['login']) . "<br>";
    echo "Role: " . htmlspecialchars($user['role']) . "<br><hr>";
}
?>