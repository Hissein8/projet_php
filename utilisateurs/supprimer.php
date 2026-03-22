<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

require_once '../menu.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: liste.php'); exit; }

$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$utilisateur) { header('Location: liste.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $db->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->execute([$id]);
    header('Location: liste.php');
    exit;
}
?>

<!-- <h2>Supprimer un utilisateur</h2>

<p>Êtes-vous sûr de vouloir supprimer 
    <strong><?= htmlspecialchars($utilisateur['prenom']) ?> <?= htmlspecialchars($utilisateur['nom']) ?></strong>
    (<?= htmlspecialchars($utilisateur['login']) ?>) ?
</p>

<form action="supprimer.php?id=<?= $id ?>" method="POST">
    <button type="submit" class="btn btn-delete">Oui, supprimer</button>
    <a href="liste.php" class="btn btn-edit">Annuler</a>
</form>

</body>
</html> -->