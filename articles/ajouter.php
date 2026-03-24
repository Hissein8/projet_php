<?php
require_once '../db.php';
require_once '../entete.php';

// if (!isset($_SESSION['username']) || ($_SESSION['username']['role'] !== 'administrateur' && $_SESSION['username']['role'] !== 'editeur')) {
//     echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
//     exit(); 
// }

require_once '../menu.php';


$categories = $db->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre   = trim($_POST['titre'] ?? '');
    $desc    = trim($_POST['description_courte'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $cat_id  = (int)($_POST['categorie_id'] ?? 0);
    $img_url = trim($_POST['image_url'] ?? '');

    // Validation PHP
    if (strlen($titre) < 3)    $erreurs[] = "Le titre doit faire au moins 3 caractères.";
    if (strlen($contenu) < 10) $erreurs[] = "Le contenu doit faire au moins 10 caractères.";
    if ($cat_id <= 0)           $erreurs[] = "Veuillez sélectionner une catégorie.";

    if (empty($erreurs)) {
        $stmt = $db->prepare("
            INSERT INTO articles (titre, description_courte, contenu, categorie_id, editeur_id, image_url)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$titre, $desc, $contenu, $cat_id, $_SESSION['user']['id'], $img_url ?: null]);
        header('Location: liste.php');
        exit;
    }
}
?>

<?php foreach ($erreurs as $err): ?>
<p class="error"><?= htmlspecialchars($err) ?></p>
<?php endforeach; ?>

<h2>Ajouter un article</h2>

<form method="POST" class="form-container" action="ajouter.php" id="form-article">

    <div class="form-group">
        <label for="titre">Titre *</label>
        <input type="text" id="titre" name="titre" required maxlength="255" value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="description_courte">Description courte</label>
        <textarea id="description_courte" name="description_courte" rows="2"><?= htmlspecialchars($_POST['description_courte'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="contenu">Contenu *</label>
        <textarea id="contenu" name="contenu" required rows="8"><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="categorie_id">Catégorie *</label>
        <select id="categorie_id" name="categorie_id" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= (int)$cat['id'] ?>"
                <?= (isset($_POST['categorie_id']) && (int)$_POST['categorie_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($cat['nom']) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="image_url">URL de l'image (optionnel)</label>
        <input type="url" id="image_url" name="image_url" placeholder="https://..."
            value="<?= htmlspecialchars($_POST['image_url'] ?? '') ?>">
    </div>

    <!-- deposer un fichier image à la place de l'URL serait une amélioration future -->
     <div class="form-group">
        <label for="image_file">Image (optionnelle)</label>
        <input type="file" id="image_file" name="image_file" accept="image/*">
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-edit">Publier</button>
        <a href="liste.php">Annuler</a>
    </div>

</form>

<script>
document.getElementById('form-article').addEventListener('submit', function(e) {
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