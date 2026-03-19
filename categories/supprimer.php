<?php
require_once '../entete.php';
require_once '../menu.php';
require_once '../db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='error'>ID de catégorie invalide.</p>";
    exit;
}
$id = (int)$_GET['id'];
$stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
$stmt->execute([$id]);
echo "<p class='success'>Catégorie supprimée avec succès.</p>";
echo "<p><a href='lister.php' class='btn'>Retour à la liste des catégories</a></p>";

?>