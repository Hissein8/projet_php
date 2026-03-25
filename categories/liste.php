<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['user']) || ($_SESSION['user']['role'] !== 'administrateur' && $_SESSION['user']['role'] !== 'editeur')) {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

require_once '../menu.php';





$categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>


<!-- // Barre de recherche (optionnelle) -->
<input type="text" id="searchInput" placeholder="Rechercher une catégorie..." class="search-input">


<a href="ajouter.php" class="btn-new">
    <i class="fa-solid fa-plus"></i> Nouvelle catégorie
</a>


<h2>Liste des catégories</h2>
    <table>
    <thead>
        <tr>            <th>ID</th>
            <th>Nom</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $categorie) : ?>
        <tr>
            <td><?= htmlspecialchars($categorie['id']) ?></td>
            <td><?= htmlspecialchars($categorie['nom']) ?></td>
            <td>
                <a href="modifier.php?id=<?= $categorie['id'] ?>" class="btn-edit">
                    <i class="fa-solid fa-pen-to-square">Modifier</i> 
                </a>
                <a href="supprimer.php?id=<?= $categorie['id'] ?>" class="btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
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
            const nom = row.cells[1].textContent.toLowerCase();
            row.style.display = nom.includes(filter) ? '' : 'none';
        });
    });
</script>

        </body>
        </html>