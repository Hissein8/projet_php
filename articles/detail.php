<?php
require_once '../db.php';
require_once '../entete.php';
require_once '../menu.php';

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ../accueil.php'); exit; }

$stmt = $db->prepare("
    SELECT a.*,
           c.nom AS categorie,
           CONCAT(u.prenom, ' ', u.nom) AS auteur
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.editeur_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) { header('Location: ../accueil.php'); exit; }

// ── Construire le chemin correct vers l'image ────────────────────────────────
// detail.php est dans /articles/, on remonte d'un niveau pour accéder à /uploads/
$imageSrc = null;
if (!empty($article['image_url'])) {
    $cheminLocal = realpath(__DIR__ . '/..') . DIRECTORY_SEPARATOR
                 . str_replace('/', DIRECTORY_SEPARATOR, $article['image_url']);
    if (file_exists($cheminLocal)) {
        $imageSrc = '../' . htmlspecialchars($article['image_url']);
    }
}
?>

<div class="detail-container">

    <h1 class="detail-titre"><?= htmlspecialchars($article['titre']) ?></h1>

    <div class="detail-meta">
        <span><i class="fa-regular fa-user"></i> <?= htmlspecialchars($article['auteur']) ?></span>
        <span><i class="fa-regular fa-calendar"></i> <?= date('d/m/Y', strtotime($article['date_publication'])) ?></span>
        <span><i class="fa-solid fa-tag"></i> <?= htmlspecialchars($article['categorie']) ?></span>
    </div>

    <hr class="detail-hr">

    <?php if ($imageSrc): ?>
        <!-- Afficher l'image de l'article en entier avec les bonnes dimensions -->
        <img src="<?= $imageSrc ?>"
             alt="<?= htmlspecialchars($article['titre']) ?>"
             class="detail-image">
    <?php endif; ?>

    <div class="detail-contenu">
        <?= nl2br(htmlspecialchars($article['contenu'])) ?>
    </div>

    <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['editeur', 'administrateur'])): ?>
        <div class="detail-actions">
            <a href="modifier.php?id=<?= (int)$article['id'] ?>" class="btn btn-edit">
                <i class="fa-solid fa-pen-to-square"></i> Modifier
            </a>
            <a href="supprimer.php?id=<?= (int)$article['id'] ?>" class="btn btn-delete"
               onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">
                <i class="fa-solid fa-trash"></i> Supprimer
            </a>
        </div>
    <?php endif; ?>

    <div class="detail-retour">
        <button class="btn-cancel">
            <a href="../accueil.php">← Retour à l'accueil</a>
        </button>
    </div>

</div>

</body>
</html>