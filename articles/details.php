<?php
require_once '../db.php';
require_once '../entete.php';
require_once '../menu.php';


$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ../accueil.php'); exit; }

$stmt = $db->prepare("
    SELECT a.*, c.nom AS categorie,
           CONCAT(u.prenom, ' ', u.nom) AS auteur
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.editeur_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$article) { header('Location: /accueil.php'); exit; }
?>

<div class="detail-container">
    <p>
        <a href="/accueil.php">← Retour à l'accueil</a> |
        <a href="/accueil.php?categorie=<?= (int)$article['categorie_id'] ?>">
            <?= htmlspecialchars($article['categorie']) ?>
        </a>
    </p>

    <?php if ($article['image_url']): ?>
    <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="detail-image">
    <?php endif; ?>

    <h1><?= htmlspecialchars($article['titre']) ?></h1>

    <p class="detail-meta">
        Par <strong><?= htmlspecialchars($article['auteur']) ?></strong>
        dans <strong><?= htmlspecialchars($article['categorie']) ?></strong>
        — <?= date('d/m/Y à H:i', strtotime($article['date_publication'])) ?>
    </p>

    <hr>

    <div class="detail-contenu">
        <?= nl2br(htmlspecialchars($article['contenu'])) ?>
    </div>

    <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['editeur', 'administrateur'])): ?>
    <div class="detail-actions">
        <a href="modifier.php?id=<?= (int)$article['id'] ?>" class="btn btn-edit">Modifier</a>
        <a href="supprimer.php?id=<?= (int)$article['id'] ?>" class="btn btn-delete"
            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">Supprimer</a>
    </div>
    <?php endif; ?>
</div>

</body>
</html>