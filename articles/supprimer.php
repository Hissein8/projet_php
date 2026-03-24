<?php
require_once '../db.php';
require_once '../entete.php';

// if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
//     echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
//     exit(); 
// }

$id = (int)($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: liste.php');
exit();