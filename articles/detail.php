<?php
require_once '../db.php';
require_once '../entete.php';
require_once '../menu.php';


$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: ../accueil.php'); exit; }



$stmt = $db->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
$article = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$article) { header('Location: ../accueil.php'); exit; }



?>

<div class="detail-container">
    <p>
        <a href="../accueil.php">← Retour à l'accueil</a> |
        <a href="../accueil.php?categorie=<?= (int)$article['categorie_id'] ?>">
            <?= htmlspecialchars($article['categorie']) ?>
        </a>
    </p>

    <h1><?= htmlspecialchars($article['titre']) ?></h1>

    <?php
    // récupérer la catégorie et l'auteur de l'article
    $categorie = $db->prepare("SELECT nom FROM categories WHERE id = ?")->execute([$article['categorie_id']])->fetch(PDO::FETCH_ASSOC);
    $auteur = $db->prepare("SELECT prenom, nom FROM utilisateurs WHERE id = ?")->execute([$article['editeur_id']])->fetch(PDO::FETCH_ASSOC);
    $article['categorie'] = $categorie ? $categorie['nom'] : 'Inconnu';
    $article['auteur'] = $auteur ? ($auteur['prenom'] . ' ' . $auteur['nom']) : 'Inconnu';
    ?>



    <h6><i class="fa-solid fa-user"></i> <?=htmlspecialchars($article['auteur'])?>
        <i class="fa-solid fa-calendar-days"></i> <?= date('d/m/Y à H:i', strtotime($article['date_publication'])) ?>
    </h6>


    <?php if ($article['image_url']): ?>
    <img src="<?= htmlspecialchars($article['image_url']) ?>" alt="<?= htmlspecialchars($article['titre']) ?>" class="detail-image">
    <?php endif; ?>


    <hr>

    <div class="detail-contenu">
        <?= nl2br(htmlspecialchars($article['contenu'])) ?>
    </div>

    <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['editeur', 'administrateur'])): ?>
    <div class="detail-actions">
        <a href="modifier.php?id=<?= (int)$article['id'] ?>" class="btn-edit"><i class="fa-solid fa-pen-to-square">Modifier</i></a>
        <a href="supprimer.php?id=<?= (int)$article['id'] ?>" class="btn-delete"
            onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')"><i class="fa-solid fa-trash">Supprimer</i></a>
                    <!-- <i class="fa-solid fa-trash">Supprimer</i> -->

    </div>
    <?php endif; ?>
</div>

</body>
</html>