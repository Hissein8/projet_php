<?php
require_once '../db.php';
require_once '../entete.php';
require_once '../menu.php';
?>

<div class="container">
    <h2>Ajouter un utilisateur</h2>
    <form action="ajouter.php" method="POST">
        <div class="form-group">
            <label for="prenom">Prénom :</label>
            <input type="text" id="prenom" name="prenom" required>
        </div>
        <div class="form-group">
            <label for="nom">Nom :</label>
            <input type="text" id="nom" name="nom" required>
        </div>
        <div class="form-group">
            <label for="login">Login :</label>
            <input type="text" id="login" name="login" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="role">Rôle :</label>
            <select id="role" name="role" required>
                <option value="">Sélectionnez un rôle</option>
                <option value="editeur">Éditeur</option>
                <option value="administrateur">Administrateur</option>
            </select>
        </div>
        <button type="submit" class="btn">Ajouter</button>
    </form>
</div>
<?php
// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $prenom = $_POST['prenom'];
        $nom = $_POST['nom'];
        $login = $_POST['login'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        // Verification de l'unicité du login
        $sql = "SELECT * FROM utilisateurs WHERE login = :login";
        $stmt = $db->prepare($sql);
        $stmt->execute([':login' => $login]);
        // on verifie si un utilisateur avec ce login existe déjà dans la base de données
        if ($stmt->fetch()) {
            echo "Ce login est déjà utilisé. Veuillez en choisir un autre.";
            exit;
        }

        // Hash du mot de passe
        $password = password_hash($password, PASSWORD_DEFAULT);

        // Insertion dans la base de données
        $sql = "INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES (:nom, :prenom, :login, :mot_de_passe, :role)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':login' => $login,
            ':mot_de_passe' => $password,
            ':role' => $role
        ]);

        $succes = "Utilisateur ajouté avec succès.";
        echo "<p style='color:green'>" . $succes . "</p>";
    } catch (Exception $e) {
        error_log($e->getMessage());
        echo "Erreur lors de l'ajout de l'utilisateur.";
    }
}
