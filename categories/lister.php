<?php
require_once '../entete.php';
require_once '../menu.php';
require_once '../db.php';


$categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>

<input type="text" id="search-input" placeholder="Rechercher par catégorie...">


<a href="ajouter.php">
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
                <a href="modifier.php?id=<?= $categorie['id'] ?>" class="btn btn-edit">
                    <i class="fa-solid fa-pen-to-square">Modifier</i> 
                </a>
                <a href="supprimer.php?id=<?= $categorie['id'] ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?');">
                    <i class="fa-solid fa-trash">Supprimer</i> 
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

        </body>
        </html>

         <script>
        // barre de recherche
        const searchInput = document.getElementById('search-input');
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase();
            const rows = document.querySelectorAll('table tbody tr');
            rows.forEach(row => {
                const nom = row.cells[1].textContent.toLowerCase();
                const prenom = row.cells[2].textContent.toLowerCase();
                if (nom.includes(query) || prenom.includes(query)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    </script>