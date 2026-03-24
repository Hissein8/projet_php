<?php
require_once '../db.php';
require_once '../entete.php';
require_once '../menu.php';

// // Vérification du rôle
// if (!isset($_SESSION['username']) || ($_SESSION['username']['role'] !== 'administrateur' && $_SESSION['username']['role'] !== 'editeur')) {
//     echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
//     exit(); 
// }

$sql = "SELECT * FROM utilisateurs";
$stmt = $db->prepare($sql);
$stmt->execute();
$utilisateurs = $stmt->fetchAll();?>

<!-- barre de recherche et le placer a droite de la page-->
<input type="text" id="search-input" placeholder="Rechercher par nom ou prénom...">


<a href="ajouter.php">
        <i class="fa-solid fa-plus"></i> Nouvel utilisateur
    </a>

    <h2>Liste des utilisateurs</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Prenom</th>
                <th>Login</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr>

<?php foreach ($utilisateurs as $user) {?>
    
    
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['nom']); ?></td>
                <td><?php echo htmlspecialchars($user['prenom']); ?></td>
                <td><?php echo htmlspecialchars($user['login']); ?></td>
                <td><?php echo htmlspecialchars($user['role']); ?></td>
                <td>
                    <a href="modifier.php?id=<?php echo $user['id']; ?>" class="btn btn-edit">Modifier</a>
                    <a href="supprimer.php?id=<?php echo $user['id']; ?>" class="btn btn-delete">Supprimer</a>
                </td>
            </tr>
        
<?php } ?>

</tbody>
    </table>

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