<?php
require_once '../db.php';
require_once '../entete.php';
require_once '../menu.php';

// Vérification du rôle
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}


$articles = $db->query("
    SELECT a.id, a.titre, a.date_publication,
           c.nom AS categorie,
           CONCAT(u.prenom, ' ', u.nom) AS auteur
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.editeur_id = u.id
    ORDER BY a.date_publication DESC
")->fetchAll();
?>

<div style="max-width:900px;margin:30px auto;padding:0 20px;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;">
        <h1>Gestion des articles</h1>
        <a href="ajouter.php" class="admin-link">+ Nouvel article</a>
    </div>

    <table style="width:100%;border-collapse:collapse;font-size:14px;">
        <thead>
            <tr style="background:#f5f5f5;border-bottom:2px solid #ddd;">
                <th style="padding:10px;text-align:left;">Titre</th>
                <th style="padding:10px;text-align:left;">Catégorie</th>
                <th style="padding:10px;text-align:left;">Auteur</th>
                <th style="padding:10px;text-align:left;">Date</th>
                <th style="padding:10px;text-align:center;">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($articles as $a): ?>
            <tr style="border-bottom:1px solid #eee;">
                <td style="padding:10px;">
                    <a href="detail.php?id=<?= (int)$a['id'] ?>"><?= e($a['titre']) ?></a>
                </td>
                <td style="padding:10px;"><?= e($a['categorie']) ?></td>
                <td style="padding:10px;"><?= e($a['auteur']) ?></td>
                <td style="padding:10px;"><?= date('d/m/Y', strtotime($a['date_publication'])) ?></td>
                <td style="padding:10px;text-align:center;display:flex;gap:6px;justify-content:center;">
                    <a href="modifier.php?id=<?= (int)$a['id'] ?>" class="admin-link"
                        style="padding:6px 12px;font-size:13px;">Modifier</a>
                    <a href="supprimer.php?id=<?= (int)$a['id'] ?>" onclick="return confirm('Supprimer cet article ?')"
                        class="admin-link"
                        style="padding:6px 12px;font-size:13px;background:#dc3545;border-color:#dc3545;">
                        Supprimer
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
</body>

</html>