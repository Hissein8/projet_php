<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: liste.php'); exit; }

// Charger l'article
$stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) { header('Location: liste.php'); exit; }

// ===== VÉRIFICATION DE PROPRIÉTÉ =====
$user       = $_SESSION['user'];
$est_auteur = ((int)$article['editeur_id'] === (int)$user['id']);
$est_admin  = ($user['role'] === 'administrateur');

if (!$est_auteur && !$est_admin) {
    echo "<p class='error'>Accès refusé. Vous ne pouvez supprimer que vos propres articles.</p>";
    exit();
}
// =====================================

// Supprimer l'image physique si elle est locale
if ($article['image_url'] && strpos($article['image_url'], '/articles/uploads/') !== false) {
    $old_path = BASE_PATH . parse_url($article['image_url'], PHP_URL_PATH);
    if (file_exists($old_path)) {
        unlink($old_path);
    }
}

// Supprimer l'article en base
$stmt = $db->prepare("DELETE FROM articles WHERE id = ?");
$stmt->execute([$id]);

header('Location: liste.php');
exit();