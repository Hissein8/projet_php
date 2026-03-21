<?php
require_once '../db.php';
require_once '../menu.php';
require_once '../entete.php';

$id = $_GET['id'];
$sql = "SELECT * FROM utilisateurs WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([':id'=>$id]);
$user = $stmt->fetch();
?>
<?php // Pourquoi POST et pas GET?
// POST est utilisé pour les actions qui modifient des données, comme la suppression, pour éviter les suppressions accidentelles via des liens GET.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { ?>
<h2>Voulez vous vraiment supprimer <?php echo htmlspecialchars($user['prenom']) . " " . htmlspecialchars($user['nom']) . " (" . htmlspecialchars($user['login']) . ")"; ?> ?</h2>
<form action="supprimer.php?id=<?php echo $id; ?>" method="post">
    <button type="submit" class="btn">Oui</button>
    <a href="liste.php" class="btn">Non</a>
</form>
<?php } ?>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sql = "DELETE FROM utilisateurs WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([':id' => $id]);
    echo "<p>Utilisateur supprimé avec succès.</p>";
    echo "<a href='liste.php' class='btn'>Retour à la liste</a>";
}
?>