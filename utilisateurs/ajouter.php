<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

require_once '../menu.php';

$erreurs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom    = trim($_POST['nom'] ?? '');
    $login  = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $role   = $_POST['role'] ?? '';

    // Validation PHP
    if (empty($prenom))   $erreurs[] = "Le prénom est obligatoire.";
    if (empty($nom))      $erreurs[] = "Le nom est obligatoire.";
    if (empty($login))    $erreurs[] = "Le login est obligatoire.";
    if (strlen($password) < 3) $erreurs[] = "Le mot de passe doit faire au moins 3 caractères.";
    if (empty($role))     $erreurs[] = "Veuillez sélectionner un rôle.";

    if (empty($erreurs)) {
        // Vérification de l'unicité du login
        $stmt = $db->prepare("SELECT id FROM utilisateurs WHERE login = ?");
        $stmt->execute([$login]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $erreurs[] = "Ce login est déjà utilisé. Veuillez en choisir un autre.";
        } else {
            // Hash du mot de passe
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);

            // Insertion dans la base de données
            $stmt = $db->prepare("INSERT INTO utilisateurs (nom, prenom, login, mot_de_passe, role) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nom, $prenom, $login, $passwordHash, $role]);

            header('Location: liste.php');
            exit;
        }
    }
}
?>

<?php foreach ($erreurs as $err): ?>
<p class="error"><?= htmlspecialchars($err) ?></p>
<?php endforeach; ?>

<h2>Ajouter un utilisateur</h2>

<form action="ajouter.php" class="form-container" method="POST">
    <div class="form-group">
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($_POST['nom'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label for="login">Login :</label>
        <input type="text" id="login" name="login" required value="<?= htmlspecialchars($_POST['login'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
    </div>
    <div class="form-group">
        <label for="role">Rôle :</label>
        <select id="role" name="role" required>
            <option value="">Sélectionnez un rôle</option>
            <option value="editeur" <?= (($_POST['role'] ?? '') === 'editeur') ? 'selected' : '' ?>>Éditeur</option>
            <option value="administrateur" <?= (($_POST['role'] ?? '') === 'administrateur') ? 'selected' : '' ?>>Administrateur</option>
        </select>
    </div>
    <div class="form-group">
        <!-- <button type="submit" class="btn btn-edit">Ajouter</button> -->
        <button type="submit" class="btn-new">
            <i class="fa-solid fa-plus">Ajouter</i>
        </button>

        <a href="liste.php">Annuler</a>
    </div>
</form>

</body>
</html>