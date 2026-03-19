<?php
require_once '../db.php';
require_once '../entete.php';
require_once '../menu.php';

//echo "<h2>Modifier un utilisateur</h2>";
$id = $_GET['id'];

$sql = 'SELECT * FROM utilisateurs WHERE id = :id';
$stmt = $db->prepare($sql);
$stmt->execute([':id' => $id]);
$user = $stmt->fetch();
?>

<div class="container">
    <h2>Modifier un utilisateur</h2>
    <form action="modifier.php?id=<?php echo $id; ?>" method="POST">
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($user['prenom']); ?>" required>
        </div>
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($user['nom']); ?>" required>
        </div>
        <div class="form-group">
            <label for="login">Login :</label>
            <input type="text" id="login" name="login" value="<?php echo    htmlspecialchars($user['login']); ?>" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe (laisser vide pour ne pas changer) :</label>
            <input type="password" id="password" name="password">
        </div>
        <div class="form-group">
            <label for="role">Rôle :</label>
            <select id="role" name="role" required>
                <option value="">Sélectionnez un rôle</option>
                <option value="editeur" <?php if ($user['role'] === 'editeur') echo 'selected'; ?>>Éditeur</option>
                <option value="administrateur" <?php if ($user['role'] === 'administrateur') echo 'selected'; ?>>Administrateur</option>
            </select>
        </div>
        <button type="submit" class="btn">Modifier</button>
    </form>

</div>

<?php 
$sql = "UPDATE utilisateurs SET prenom = :prenom, nom = :nom, login = :login, role = :role WHERE id = :id";
$stmt = $db->prepare($sql);
$stmt->execute([
    ':prenom' => $_POST['prenom'],
    ':nom' => $_POST['nom'],
    ':login' => $_POST['login'],
    //':mot_de_passe' => password_hash($_POST['password'], PASSWORD_DEFAULT),
    ':role' => $_POST['role'],
    ':id' => $id
]);

if (!empty($_POST['password'])) {
    $sql = "UPDATE utilisateurs SET mot_de_passe = :mot_de_passe WHERE id = :id";
    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':mot_de_passe' => password_hash($_POST['password'], PASSWORD_DEFAULT),
        ':id' => $id
    ]);
}

echo "Modification effectue avec succes";

?>