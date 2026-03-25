<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
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

// ===== VÉRIFICATION DE PROPRIÉTÉ =====
$user       = $_SESSION['user'];
$est_auteur = ((int)$article['editeur_id'] === (int)$user['id']);
$est_admin  = ($user['role'] === 'administrateur');

if (!$est_auteur && !$est_admin) {
    echo "<p class='error'>Accès refusé. Vous ne pouvez modifier que vos propres articles.</p>";
    exit();
}
// =====================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre   = trim($_POST['titre'] ?? '');
    $desc    = trim($_POST['description_courte'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $cat_id  = (int)($_POST['categorie_id'] ?? 0);

    if (strlen($titre) < 3)    $erreurs[] = "Le titre doit faire au moins 3 caractères.";
    if (strlen($contenu) < 10) $erreurs[] = "Le contenu doit faire au moins 10 caractères.";
    if ($cat_id <= 0)           $erreurs[] = "Veuillez sélectionner une catégorie.";

    $image_path = $article['image_url'];

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {

        $file = $_FILES['image_file'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = [
                UPLOAD_ERR_INI_SIZE   => "L'image dépasse la taille maximale autorisée par le serveur.",
                UPLOAD_ERR_FORM_SIZE  => "L'image dépasse la taille maximale autorisée par le formulaire.",
                UPLOAD_ERR_PARTIAL    => "L'image n'a été que partiellement uploadée.",
                UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant.",
                UPLOAD_ERR_CANT_WRITE => "Impossible d'écrire l'image sur le disque.",
                UPLOAD_ERR_EXTENSION  => "Upload bloqué par une extension PHP.",
            ];
            $erreurs[] = $upload_errors[$file['error']] ?? "Erreur inconnue lors de l'upload.";
        }

        $max_size = 3 * 1024 * 1024;
        if ($file['error'] === UPLOAD_ERR_OK && $file['size'] > $max_size) {
            $erreurs[] = "L'image ne doit pas dépasser 3 Mo (taille reçue : " . round($file['size'] / 1024) . " Ko).";
        }

        if ($file['error'] === UPLOAD_ERR_OK) {
            $finfo     = new finfo(FILEINFO_MIME_TYPE);
            $mime_type = $finfo->file($file['tmp_name']);

            $allowed_types = [
                'image/jpeg' => 'jpg',
                'image/png'  => 'png',
                'image/webp' => 'webp',
                'image/gif'  => 'gif',
            ];

            if (!array_key_exists($mime_type, $allowed_types)) {
                $erreurs[] = "Format d'image non autorisé. Formats acceptés : JPG, PNG, WEBP, GIF.";
            }
        }

        if (empty($erreurs) && strlen($titre) >= 3) {

            $nom_slug = strtolower(trim($titre));
            $nom_slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nom_slug);
            $nom_slug = preg_replace('/[^a-z0-9]+/', '_', $nom_slug);
            $nom_slug = trim($nom_slug, '_');

            $id_prefix   = $cat_id . '_' . time();
            $extension   = $allowed_types[$mime_type];
            $filename    = $id_prefix . '_' . $nom_slug . '.' . $extension;
            $upload_dir  = BASE_PATH . '/articles/uploads/';
            $target_path = $upload_dir . $filename;

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                // Supprimer l'ancienne image si elle est locale
                if ($article['image_url'] && strpos($article['image_url'], '/articles/uploads/') !== false) {
                    $old_path = BASE_PATH . parse_url($article['image_url'], PHP_URL_PATH);
                    if (file_exists($old_path)) {
                        unlink($old_path);
                    }
                }
                $image_path = BASE_URL . '/articles/uploads/' . $filename;
            } else {
                $erreurs[] = "Erreur lors de l'enregistrement de l'image. Vérifiez les permissions du dossier uploads/.";
            }
        }
    }

    if (empty($erreurs)) {
        $stmt = $db->prepare("
            UPDATE articles
            SET titre = ?, description_courte = ?, contenu = ?,
                categorie_id = ?, image_url = ?
            WHERE id = ?
        ");
        $stmt->execute([$titre, $desc, $contenu, $cat_id, $image_path, $id]);
        header('Location: liste.php');
        exit;
    }

    $article = array_merge($article, $_POST);
}
?>

<?php foreach ($erreurs as $err): ?>
<p class="error"><?= htmlspecialchars($err) ?></p>
<?php endforeach; ?>

<h2>Modifier l'article</h2>

<form method="POST" class="form-container" action="modifier.php?id=<?= $id ?>" id="form-modifier" enctype="multipart/form-data">

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
        <label for="image_file">Image</label>
        <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif">
        <small>Laisser vide pour garder l'image actuelle — Formats : JPG, PNG, WEBP, GIF — Max : 3 Mo</small>

        <?php if ($article['image_url']): ?>
        <div style="margin-top: 10px;">
            <p><small>Image actuelle :</small></p>
            <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="Image actuelle"
                 style="max-width: 200px; max-height: 200px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
        <?php endif; ?>

        <div id="preview-container" style="display:none; margin-top: 10px;">
            <p><small>Nouvelle image :</small></p>
            <img id="image-preview" src="#" alt="Aperçu"
                 style="max-width: 200px; max-height: 200px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
    </div>

    <div class="form-group">
        <button type="submit" class="btn btn-edit">Enregistrer</button>
        <a href="liste.php">Annuler</a>
    </div>

</form>

<!-- Le JavaScript reste identique -->