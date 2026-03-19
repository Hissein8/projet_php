<?php
require_once '../entete.php';
require_once '../menu.php';
require_once '../db.php';


$categories = $db->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
?>



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