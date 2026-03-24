<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['username']) || ($_SESSION['role'] !== 'administrateur' && $_SESSION['role'] !== 'editeur')) {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

require_once '../menu.php';


$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: liste.php'); exit; }

$categories = $db->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$erreurs = [];

// Charger l'article
$stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) { header('Location: liste.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre   = trim($_POST['titre'] ?? '');
    $desc    = trim($_POST['description_courte'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $cat_id  = (int)($_POST['categorie_id'] ?? 0);
    $img_url = trim($_POST['image_url'] ?? '');

    if (strlen($titre) < 3)    $erreurs[] = "Le titre doit faire au moins 3 caractères.";
    if (strlen($contenu) < 10) $erreurs[] = "Le contenu doit faire au moins 10 caractères.";
    if ($cat_id <= 0)           $erreurs[] = "Veuillez sélectionner une catégorie.";

    if (empty($erreurs)) {
        $stmt = $db->prepare("
            UPDATE articles
            SET titre = ?, description_courte = ?, contenu = ?,
                categorie_id = ?, image_url = ?
            WHERE id = ?
        ");
        $stmt->execute([$titre, $desc, $contenu, $cat_id, $img_url ?: null, $id]);
        header('Location: liste.php');
        exit;
    }
    // Pré-remplir depuis POST si erreur
    $article = array_merge($article, $_POST);
}
?>

<?php foreach ($erreurs as $err): ?>
<p class="error"><?= htmlspecialchars($err) ?></p>
<?php endforeach; ?>

<h2>Modifier l'article</h2>

<form method="POST" class="form-container" action="modifier.php?id=<?= $id ?>" id="form-modifier">

    <div class="form-group">
        <label for="titre">Titre *</label>
        <input type="text" id="titre" name="titre" required maxlength="255" value="<?= htmlspecialchars($article['titre']) ?>">
    </div>

    <div class="form-group">
        <label for="description_courte">Description courte</label>
        <textarea id="description_courte" name="description_courte" rows="2"><?= htmlspecialchars($article['description_courte']) ?></textarea>
    </div>

    <div class="form-group">
        <label for="contenu">Contenu *</label>
        <textarea id="contenu" name="contenu" required rows="8"><?= htmlspecialchars($article['contenu']) ?></textarea>
    </div>

    <div class="form-group">
        <label for="categorie_id">Catégorie *</label>
        <select id="categorie_id" name="categorie_id" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>"
                <?= (int)$cat['id'] === (int)$article['categorie_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nom']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="image_url">URL de l'image</label>
        <input type="url" id="image_url" name="image_url" value="<?= htmlspecialchars($article['image_url'] ?? '') ?>">
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-edit">Enregistrer</button>
        <a href="liste.php">Annuler</a>
    </div>

</form>

<script>
document.getElementById('form-modifier').addEventListener('submit', function(e) {
    const titre = document.getElementById('titre').value.trim();
    const contenu = document.getElementById('contenu').value.trim();
    const cat = document.getElementById('categorie_id').value;
    const errors = [];
    if (titre.length < 3) errors.push('Le titre doit faire au moins 3 caractères.');
    if (contenu.length < 10) errors.push('Le contenu doit faire au moins 10 caractères.');
    if (!cat) errors.push('Veuillez sélectionner une catégorie.');
    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join('\n'));
    }
});
</script>

</body>
</html>