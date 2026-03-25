<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

require_once '../menu.php';


$utilisateurs = $db->query("SELECT * FROM utilisateurs ORDER BY nom")->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- Recherche par nom -->
<input type="text" id="search" placeholder="Rechercher par nom..." class="search

<a href="ajouter.php" class="btn-new">
    <i class="fa-solid fa-plus"></i> Nouvel utilisateur
</a>

<h2>Liste des utilisateurs</h2>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Login</th>
            <th>Rôle</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($utilisateurs as $utilisateur) : ?>
        <tr>
            <td><?= htmlspecialchars($utilisateur['id']) ?></td>
            <td><?= htmlspecialchars($utilisateur['nom']) ?></td>
            <td><?= htmlspecialchars($utilisateur['prenom']) ?></td>
            <td><?= htmlspecialchars($utilisateur['login']) ?></td>
            <td><?= htmlspecialchars($utilisateur['role']) ?></td>
            <td>
                <a href="modifier.php?id=<?= (int)$utilisateur['id'] ?>" class="btn btn-edit">
                    <i class="fa-solid fa-pen-to-square">Modifier</i>
                </a>
                <a href="supprimer.php?id=<?= (int)$utilisateur['id'] ?>" class="btn btn-delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
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
// Fonction de recherche en temps réel
document.getElementById('search').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        const nom = row.cells[1].textContent.toLowerCase();
        const prenom = row.cells[2].textContent.toLowerCase();
        if (nom.includes(searchTerm) || prenom.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    
    });
});
</script>