<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

require_once '../menu.php';

// $id = (int)($_GET['id'] ?? 0);
// if ($id <= 0) { header('Location: liste.php'); exit; }

// $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
// $stmt->execute([$id]);
// $utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
// if (!$utilisateur) { header('Location: liste.php'); exit; }

// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");
//     $stmt->execute([$id]);
//     header('Location: liste.php');
//     exit;
// }
// ?>

<?php
$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    // Optionnel : ajouter un message de succès ou d'erreur
    $_SESSION['message'] = "L'utilisateur a été supprimé avec succès.";
}   else {
    // Optionnel : ajouter un message d'erreur si l'ID est invalide
    $_SESSION['error'] = "ID utilisateur invalide.";
}

header('Location: liste.php');
exit();