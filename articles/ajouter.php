<?php
require_once '../db.php';
require_once '../entete.php';


// ===== CONTRÔLE D'ACCÈS =====
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit();
}


// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//     var_dump($_FILES);
//     var_dump($_POST);
//     die(); // stoppe l'exécution pour voir le résultat
// }


require_once '../menu.php';

// ===== INITIALISATION =====
$categories = $db->query("SELECT * FROM categories ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
$erreurs = [];

// ===== TRAITEMENT DU FORMULAIRE =====
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titre   = trim($_POST['titre'] ?? '');
    $desc    = trim($_POST['description_courte'] ?? '');
    $contenu = trim($_POST['contenu'] ?? '');
    $cat_id  = (int)($_POST['categorie_id'] ?? 0);
    //$img_url = trim($_POST['image_url'] ?? '');

    // --- Validation PHP ---
    if (strlen($titre) < 3)    $erreurs[] = "Le titre doit faire au moins 3 caractères.";
    if (strlen($contenu) < 10) $erreurs[] = "Le contenu doit faire au moins 10 caractères.";
    if ($cat_id <= 0)           $erreurs[] = "Veuillez sélectionner une catégorie.";

    // ===== GESTION DE L'UPLOAD D'IMAGE =====
    $image_path = null;

    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {

        $file = $_FILES['image_file'];

        // 1. Vérification des erreurs PHP d'upload
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

        // 2. Vérification de la taille (max 3 Mo)
        $max_size = 3 * 1024 * 1024;
        if ($file['error'] === UPLOAD_ERR_OK && $file['size'] > $max_size) {
            $erreurs[] = "L'image ne doit pas dépasser 3 Mo (taille reçue : " . round($file['size'] / 1024) . " Ko).";
        }

        // 3. Vérification du type MIME réel via finfo (non falsifiable)
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

        // 4. Construction du nom de fichier et déplacement si tout est valide
        if (empty($erreurs) && strlen($titre) >= 3) {

            // Formatage du titre en slug pour le nom de fichier
            $nom_slug = strtolower(trim($titre));
            $nom_slug = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nom_slug);
            $nom_slug = preg_replace('/[^a-z0-9]+/', '_', $nom_slug);
            $nom_slug = trim($nom_slug, '_');

            // ID unique basé sur la catégorie
                $id_prefix = $cat_id . '_' . time();
            //$id_prefix = $cat_id . '_' . time();

            // Extension depuis le type MIME réel
            $extension = $allowed_types[$mime_type];

            // Nom final : ex. "id_moncategorie_php.jpg"
            $filename = $id_prefix . '_' . $nom_slug . '.' . $extension;
            // $filename = $id_prefix . '_' . $nom_slug . '.' . $extension;

            // Dossier de destination
            // $upload_dir  = __DIR__ . '/projet_php/articles/uploads/';
            $upload_dir  = __DIR__ . '/uploads/';
            $target_path = $upload_dir . $filename;

            // Création du dossier si inexistant
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Déplacement sécurisé
            if (move_uploaded_file($file['tmp_name'], $target_path)) {
                $image_path = 'uploads/' . $filename;
            } else {
                $erreurs[] = "Erreur lors de l'enregistrement de l'image. Vérifiez les permissions du dossier uploads/.";
            }
        }
    }

    // ===== INSERTION EN BASE DE DONNÉES =====
    if (empty($erreurs)) {
        try {
            // $stmt = $db->prepare("
                // INSERT INTO articles (titre, description_courte, contenu, categorie_id, editeur_id, image_url, image_file)
                // VALUES (?, ?, ?, ?, ?, ?, ?)
            // ");
            $stmt = $db->prepare("
                INSERT INTO articles (titre, description_courte, contenu, categorie_id, editeur_id, image_url)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->execute([
                $titre,
                $desc,
                $contenu,
                $cat_id,
                $_SESSION['user']['id'],
                // $img_url ?: null,
                $image_path
            ]);

            header('Location: liste.php');
            exit();

        } catch (PDOException $e) {
            $erreurs[] = "Erreur lors de l'enregistrement en base de données : " . $e->getMessage();
            // Supprimer l'image si l'insertion échoue pour éviter les fichiers orphelins
            if ($image_path && file_exists(__DIR__ . '/' . $image_path)) {
                unlink(__DIR__ . '/' . $image_path);
            }
        }
    }
}
?>

<?php foreach ($erreurs as $err): ?>
    <p class="error"><?= htmlspecialchars($err) ?></p>
<?php endforeach; ?>

<h2>Ajouter un article</h2>

<form method="POST" class="form-container" action="ajouter.php" id="form-article" enctype="multipart/form-data">

    <input type="hidden" name="MAX_FILE_SIZE" value="2097152">

    <div class="form-group">
        <label for="titre">Titre *</label>
        <input type="text" id="titre" name="titre" required maxlength="255"
               value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>">
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
            <?php foreach ($categories as $cat){ ?>
                <option value="<?= (int)$cat['id'] ?>"
                    <?= (isset($_POST['categorie_id']) && (int)$_POST['categorie_id'] === (int)$cat['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat['nom']) ?>
                </option>
            <?php }; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="image_file">Image</label>
        <input type="file" id="image_file" name="image_file" accept="image/jpeg,image/png,image/webp,image/gif">
        <small>Formats acceptés : JPG, PNG, WEBP, GIF — Taille max : 2 Mo</small>

        <div id="preview-container" style="display:none; margin-top: 10px;">
            <img id="image-preview" src="#" alt="Aperçu"
                 style="max-width: 200px; max-height: 200px; border: 1px solid #ccc; border-radius: 4px;">
        </div>
    </div>

    <button type="submit" class="btn-new">
            <i class="fa-solid fa-plus"></i> Publier
    </button>

    <a href="liste.php" class="btn-cancel">
            <i class="fa-solid fa-xmark">Annuler</i>
    </a>

</form>

<script>
    // Aperçu de l'image + validation immédiate à la sélection
    document.getElementById('image_file').addEventListener('change', function () {
        const file = this.files[0];
        const preview = document.getElementById('image-preview');
        const container = document.getElementById('preview-container');

        if (file) {
            const allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];

            if (!allowedTypes.includes(file.type)) {
                alert('Format non autorisé. Utilisez JPG, PNG, WEBP ou GIF.');
                this.value = '';
                container.style.display = 'none';
                return;
            }

            if (file.size > 2 * 1024 * 1024) {
                alert("L'image dépasse 2 Mo. Veuillez choisir une image plus légère.");
                this.value = '';
                container.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                preview.src = e.target.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            container.style.display = 'none';
        }
    });

    // Validation à la soumission
    document.getElementById('form-article').addEventListener('submit', function (e) {
        const titre = document.getElementById('titre').value.trim();
        const contenu = document.getElementById('contenu').value.trim();
        const cat = document.getElementById('categorie_id').value;
        const errors = [];

        if (titre.length < 3)    errors.push('Le titre doit faire au moins 3 caractères.');
        if (contenu.length < 10) errors.push('Le contenu doit faire au moins 10 caractères.');
        if (!cat)                 errors.push('Veuillez sélectionner une catégorie.');

        if (errors.length > 0) {
            e.preventDefault();
            alert(errors.join('\n'));
        }
    });
</script>

</body>
</html>