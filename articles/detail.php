<?php
require_once '../db.php';
require_once '../entete.php';
require_once '../menu.php';


$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ../accueil.php'); exit; }

$stmt = $db->prepare("
    SELECT a.*, c.nom AS categorie, c.slug AS categorie_slug,
           CONCAT(u.prenom, ' ', u.nom) AS auteur
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.editeur_id = u.id
    WHERE a.id = ?
");
$stmt->execute([$id]);
$article = $stmt->fetch();

if (!$article) { header('Location: ../accueil.php'); exit; }
?>

<div style="max-width:800px;margin:30px auto;padding:0 20px;">
    <p>
        <a href="../accueil.php">← Retour à l'accueil</a> |
        <a href="../accueil.php?categorie=<?= e($article['categorie_slug']) ?>">
            <?= e($article['categorie']) ?>
        </a>
    </p>

    <?php if ($article['image_url']): ?>
    <img src="<?= e($article['image_url']) ?>" alt="<?= e($article['titre']) ?>"
        style="width:100%;max-height:350px;object-fit:cover;border-radius:8px;margin-bottom:20px;">
    <?php endif; ?>

    <h1><?= e($article['titre']) ?></h1>

    <p style="color:#888;font-size:14px;">
        Par <strong><?= e($article['auteur']) ?></strong>
        dans <strong><?= e($article['categorie']) ?></strong>
        — <?= date('d/m/Y à H:i', strtotime($article['date_publication'])) ?>
    </p>

    <hr>
    <div style="line-height:1.8;font-size:16px;">
        <?= nl2br(e($article['contenu'])) ?>
    </div>

    <?php if (isset($_SESSION['user_role']) &&
              in_array($_SESSION['user_role'], ['editeur', 'administrateur'])): ?>
    <div style="margin-top:30px;display:flex;gap:12px;">
        <a href="modifier.php?id=<?= (int)$article['id'] ?>" class="admin-link">Modifier</a>
        <a href="supprimer.php?id=<?= (int)$article['id'] ?>" class="admin-link"
            onclick="return confirm('Supprimer cet article ?')"
            style="background:#dc3545;border-color:#dc3545;">Supprimer</a>
    </div>
    <?php endif; ?>
</div>
</body>

</html>