<?php
require_once '../db.php';
require_once '../entete.php';
require_once '../menu.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

function e($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: liste.php'); exit; }

$categories = $db->query("SELECT * FROM categories ORDER BY nom")->fetchAll();
$erreurs = [];

// Charger l'article
$stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch();
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

<div style="max-width:700px;margin:30px auto;padding:0 20px;">
    <h1>Modifier l'article</h1>

    <?php foreach ($erreurs as $err): ?>
    <p style="color:red;background:#fff0f0;padding:10px;border-radius:6px;margin-bottom:8px;">
        <?= e($err) ?>
    </p>
    <?php endforeach; ?>

    <!-- <form method="POST" action="" id="form-modifier" novalidate style="display:flex;flex-direction:column;gap:16px;"> -->
    <form method="POST" class="form-container" action="modifier.php?id=<?= $id ?>" id="form-modifier">
        <div class="form-group">
            <label for="titre">Titre *</label>
            <input type="text" id="titre" name="titre" required maxlength="255" value="<?= e($article['titre']) ?>">
        </div>
        <div class="form-group">
            <label for="description_courte">Description courte</label>
            <textarea id="description_courte" name="description_courte" rows="2"
                style="width:100%;padding:10px;border:2px solid #ddd;border-radius:8px;"><?= e($article['description_courte']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="contenu">Contenu *</label>
            <textarea id="contenu" name="contenu" required rows="8"
                style="width:100%;padding:10px;border:2px solid #ddd;border-radius:8px;"><?= e($article['contenu']) ?></textarea>
        </div>
        <div class="form-group">
            <label for="categorie_id">Catégorie *</label>
            <select id="categorie_id" name="categorie_id" required>
                <option value="">-- Choisir --</option>
                <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>"
                    <?= (int)$cat['id'] === (int)$article['categorie_id'] ? 'selected' : '' ?>>
                    <?= e($cat['nom']) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="image_url">URL de l'image</label>
            <input type="url" id="image_url" name="image_url" value="<?= e($article['image_url'] ?? '') ?>">
        </div>
        <div style="display:flex;gap:12px;">
            <button type="submit" class="login-btn" style="width:auto;padding:12px 30px;margin:0;">
                Enregistrer
            </button>
            <a href="liste.php" style="padding:12px 20px;color:#666;">Annuler</a>
        </div>
    </form>
</div>

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