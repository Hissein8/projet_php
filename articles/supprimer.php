<?php
require_once '../db.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

// Note : session_start() est dans db.php via entete.php,
// mais ici on n'inclut pas entete pour éviter l'affichage HTML.
// On démarre la session manuellement si besoin.
if (session_status() === PHP_SESSION_NONE) session_start();

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: liste.php');
exit();