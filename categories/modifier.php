<?php
require_once '../entete.php';
require_once '../menu.php';
require_once '../db.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: liste.php');
    exit;
}

$id = $_GET['id'];


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);

    if (!empty($nom)) {
        $stmt = $db->prepare("UPDATE categories SET nom = ? WHERE id = ?");
        $stmt->execute([$nom, $id]);
        header('Location: liste.php');
        exit;
    }
} else {
    $stmt = $db->prepare("SELECT * FROM categories WHERE id = ?");
    $stmt->execute([$id]);
    $categorie = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categorie) {
        header('Location: lister.php');
        exit;
    }
}
?>

<h2>Modifier la catégorie</h2>
<form action="" method="POST" class="form-container">
    <div class="form-group">
        <label for="nom">Nom de la catégorie :</label>
        <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($categorie['nom']) ?>">
    </div>
    <div class="form-actions">
        <button type="submit" class="btn-edit">
            <i class="fa-solid fa-pen-to-square">Modifier</i>
        </button>
        <a href="liste.php" class="btn-cancel">
            <i class="fa-solid fa-xmark">Annuler</i>
        </a>
    </div>
</form>

        </body>
        </html>