<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires.</p>";
    exit();
}

require_once '../menu.php';

$categories = $db->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$erreurs    = [];

// ── Configuration de l'upload ────────────────────────────────────────────────
$uploadDir      = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;
$uploadMax      = 2 * 1024 * 1024; // 2 Mo
$typesAutorises = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
$extsAutorisees = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

// ── Créer le dossier uploads/ s'il n'existe pas ──────────────────────────────
if (!is_dir($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        $erreurs[] = "Impossible de créer le dossier uploads/. Créez-le manuellement à la racine du projet.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre   = trim($_POST['titre']              ?? '');
    $desc    = trim($_POST['description_courte'] ?? '');
    $contenu = trim($_POST['contenu']            ?? '');
    $cat_id  = (int)($_POST['categorie_id']      ?? 0);

    // ── Validation des champs texte ──────────────────────────────────────────
    if (strlen($titre) < 3)    $erreurs[] = "Le titre doit faire au moins 3 caractères.";
    if (strlen($contenu) < 10) $erreurs[] = "Le contenu doit faire au moins 10 caractères.";
    if ($cat_id <= 0)           $erreurs[] = "Veuillez sélectionner une catégorie.";

    // ── Traitement de l'image ────────────────────────────────────────────────
    $image_url    = null;
    $imageEnvoyee = isset($_FILES['image'])
                    && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE
                    && $_FILES['image']['size']  > 0;

    if ($imageEnvoyee) {

        $file = $_FILES['image'];

        // 1. Code d'erreur PHP upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $codesErreur = [
                UPLOAD_ERR_INI_SIZE   => "Le fichier dépasse la limite du serveur (php.ini).",
                UPLOAD_ERR_FORM_SIZE  => "Le fichier dépasse la limite du formulaire.",
                UPLOAD_ERR_PARTIAL    => "Le fichier n'a été que partiellement uploadé.",
                UPLOAD_ERR_NO_TMP_DIR => "Dossier temporaire manquant sur le serveur.",
                UPLOAD_ERR_CANT_WRITE => "Impossible d'écrire le fichier sur le serveur.",
            ];
            $erreurs[] = $codesErreur[$file['error']] ?? "Erreur d'upload inconnue (code {$file['error']}).";

        } else {

            // 2. Taille du fichier
            if ($file['size'] > $uploadMax) {
                $erreurs[] = "L'image ne doit pas dépasser " . ($uploadMax / 1024 / 1024) . " Mo "
                           . "(votre fichier fait " . round($file['size'] / 1024 / 1024, 2) . " Mo).";
            }

            // 3. Type MIME réel (lecture des octets, pas la déclaration du navigateur)
            $finfo    = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->file($file['tmp_name']);
            if (!in_array($mimeType, $typesAutorises)) {
                $erreurs[] = "Type de fichier non autorisé ($mimeType). "
                           . "Formats acceptés : JPEG, PNG, WebP, GIF.";
            }

            // 4. Extension du fichier
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $extsAutorisees)) {
                $erreurs[] = "Extension .$ext non autorisée. "
                           . "Extensions acceptées : " . implode(', ', $extsAutorisees) . ".";
            }

            // 5. Déplacer le fichier si tout est valide
            if (empty($erreurs)) {

                // Récupérer le nom de la catégorie pour le nom du fichier
                $stmtCat = $db->prepare("SELECT nom FROM categories WHERE id = ?");
                $stmtCat->execute([$cat_id]);
                $catRow = $stmtCat->fetch(PDO::FETCH_ASSOC);

                // Nettoyer le nom (sans accents, sans espaces)
                $nomCat     = $catRow ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $catRow['nom']) : 'categorie';

                // Format : idCategorie_nomCategorie_timestamp.extension
                // Exemple : 1_Technologie_1742850123.jpg
                $nomFichier  = $cat_id . '_' . $nomCat . '_' . time() . '.' . $ext;
                $destination = $uploadDir . $nomFichier;

                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $image_url = 'uploads/' . $nomFichier;
                } else {
                    $erreurs[] = "Échec du déplacement du fichier. "
                               . "Vérifiez que le dossier uploads/ existe et est accessible en écriture. "
                               . "(Chemin attendu : $destination)";
                }
            }
        }
    }

    // ── Insertion en base uniquement si aucune erreur ────────────────────────
    if (empty($erreurs)) {
        $stmt = $db->prepare("
            INSERT INTO articles
                (titre, description_courte, contenu, categorie_id, editeur_id, image_url)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([
            $titre,
            $desc,
            $contenu,
            $cat_id,
            $_SESSION['user']['id'],
            $image_url,
        ]);
        header('Location: liste.php');
        exit;
    }
}
?>

<?php foreach ($erreurs as $err): ?>
    <p class="error" style="color:#dc3545; background:#fdf0f0; border:1px solid #dc3545;
       border-radius:4px; padding:10px; margin-bottom:8px;">
        <i class="fa-solid fa-circle-exclamation"></i> <?= htmlspecialchars($err) ?>
    </p>
<?php endforeach; ?>

<h2>Ajouter un article</h2>

<!-- enctype="multipart/form-data" obligatoire pour l'upload de fichier -->
<form method="POST" class="form-container" action="ajouter.php"
      id="form-article" enctype="multipart/form-data">

    <div class="form-group">
        <label for="titre">Titre *</label>
        <input type="text" id="titre" name="titre" required maxlength="255"
               value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
    </div>

    <div class="form-group">
        <label for="description_courte">Description courte</label>
        <textarea id="description_courte" name="description_courte"
                  rows="2"><?= htmlspecialchars($_POST['description_courte'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="contenu">Contenu *</label>
        <textarea id="contenu" name="contenu" required
                  rows="8"><?= htmlspecialchars($_POST['contenu'] ?? '') ?></textarea>
    </div>

    <div class="form-group">
        <label for="categorie_id">Catégorie *</label>
        <select id="categorie_id" name="categorie_id" required>
            <option value="">-- Choisir --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= (int)$cat['id'] ?>"
                    <?= (isset($_POST['categorie_id']) && (int)$_POST['categorie_id'] === (int)$cat['id'])
                        ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nom']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="image">
            Image de l'article
            <small>(optionnel — JPEG, PNG, WebP, GIF — max 2 Mo)</small>
        </label>
        <input type="file" id="image" name="image"
               accept="image/jpeg,image/png,image/webp,image/gif">

        <!-- Prévisualisation avant envoi -->
        <div id="preview-container" style="display:none; margin-top:10px;">
            <p style="margin:0 0 4px;"><strong>Aperçu :</strong></p>
            <img id="preview-img" src="" alt="Aperçu"
                 style="max-width:300px; max-height:200px;
                        border-radius:6px; border:1px solid #ccc;">
        </div>
    </div>

    <button type="submit" class="btn-new">
        <i class="fa-solid fa-plus"></i> Publier
    </button>

    <a href="liste.php" class="btn-cancel">
        <i class="fa-solid fa-xmark"></i> Annuler
    </a>

</form>

<script>
// ── Prévisualisation avant soumission ─────────────────────────────────────────
document.getElementById('image').addEventListener('change', function () {
    const file      = this.files[0];
    const preview   = document.getElementById('preview-img');
    const container = document.getElementById('preview-container');

    if (file) {
        const reader = new FileReader();
        reader.onload = e => {
            preview.src             = e.target.result;
            container.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        container.style.display = 'none';
    }
});

// ── Validation côté client ────────────────────────────────────────────────────
document.getElementById('form-article').addEventListener('submit', function (e) {
    const titre   = document.getElementById('titre').value.trim();
    const contenu = document.getElementById('contenu').value.trim();
    const cat     = document.getElementById('categorie_id').value;
    const file    = document.getElementById('image').files[0];
    const errors  = [];

    if (titre.length < 3)    errors.push('Le titre doit faire au moins 3 caractères.');
    if (contenu.length < 10) errors.push('Le contenu doit faire au moins 10 caractères.');
    if (!cat)                 errors.push('Veuillez sélectionner une catégorie.');

    if (file) {
        const maxSize  = 2 * 1024 * 1024;
        const typesOk  = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

        if (file.size > maxSize) {
            errors.push(`L'image ne doit pas dépasser 2 Mo (votre fichier : ${(file.size / 1024 / 1024).toFixed(2)} Mo).`);
        }
        if (!typesOk.includes(file.type)) {
            errors.push('Format non autorisé. Utilisez : JPEG, PNG, WebP ou GIF.');
        }
    }

    if (errors.length > 0) {
        e.preventDefault();
        alert(errors.join('\n'));
    }
});
</script>

</body>
</html>