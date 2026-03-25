<?php
require_once '../db.php';
require_once '../entete.php';

// Vérification du rôle
if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

require_once '../menu.php';


$articles = $db->query("
    SELECT a.id, a.titre, a.date_publication,
           c.nom AS categorie,
           CONCAT(u.prenom, ' ', u.nom) AS auteur
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.editeur_id = u.id
    ORDER BY a.date_publication DESC
")->fetchAll(PDO::FETCH_ASSOC);


// $articles = $db->query("SELECT * FROM articles ORDER BY date_publication DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- // Barre de recherche (optionnelle) -->
<input type="text" id="searchInput" placeholder="Rechercher un article..." class="search-input">

<a href="ajouter.php" class="btn-new">
    <i class="fa-solid fa-plus"></i> Nouvel article
</a>

<h2>Gestion des articles</h2>
<table>
    <thead>
        <tr>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Auteur</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $a) : ?>
        <tr>
            <td>
                <a href="detail.php?id=<?= (int)$a['id'] ?>"><?= htmlspecialchars($a['titre']) ?></a>
            </td>
            <td><?= htmlspecialchars($a['categorie']) ?></td>
            <td><?= htmlspecialchars($a['auteur']) ?></td>
            <td><?= date('d/m/Y', strtotime($a['date_publication'])) ?></td>
            <td>
                <a href="modifier.php?id=<?= (int)$a['id'] ?>" class="btn-edit">
                    <i class="fa-solid fa-pen-to-square">Modifier</i>
                </a>
                <a href="supprimer.php?id=<?= (int)$a['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?');">
                    <i class="fa-solid fa-trash">Supprimer</i>
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
    // barre de recherche (optionnelle)
    document.getElementById('searchInput').addEventListener('input', function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll('table tbody tr');
        rows.forEach(row => {
            const title = row.querySelector('td:first-child').textContent.toLowerCase();
            row.style.display = title.includes(filter) ? '' : 'none';
        });
    });
</script>


</body>
</html>