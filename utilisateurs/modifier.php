<?php
require_once '../db.php';
require_once '../entete.php';

if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'administrateur') {
    echo "<p class='error'>Accès refusé. Vous n'avez pas les permissions nécessaires pour accéder à cette page.</p>";
    exit(); 
}

require_once '../menu.php';

$erreurs = [];

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { header('Location: liste.php'); exit; }

$stmt = $db->prepare("SELECT * FROM utilisateurs WHERE id = ?");
$stmt->execute([$id]);
$utilisateur = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$utilisateur) { header('Location: liste.php'); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = trim($_POST['prenom'] ?? '');
    $nom    = trim($_POST['nom'] ?? '');
    $login  = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';
    $role   = $_POST['role'] ?? '';

    // Validation PHP
    if (empty($prenom)) $erreurs[] = "Le prénom est obligatoire.";
    if (empty($nom))    $erreurs[] = "Le nom est obligatoire.";
    if (empty($login))  $erreurs[] = "Le login est obligatoire.";
    if (empty($role))   $erreurs[] = "Veuillez sélectionner un rôle.";

    // Vérification unicité du login (sauf pour l'utilisateur actuel)
    if (empty($erreurs)) {
        $stmt = $db->prepare("SELECT id FROM utilisateurs WHERE login = ? AND id != ?");
        $stmt->execute([$login, $id]);
        if ($stmt->fetch(PDO::FETCH_ASSOC)) {
            $erreurs[] = "Ce login est déjà utilisé. Veuillez en choisir un autre.";
        }
    }

    if (empty($erreurs)) {
        $stmt = $db->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, login = ?, role = ? WHERE id = ?");
        $stmt->execute([$prenom, $nom, $login, $role, $id]);

        // Mise à jour du mot de passe seulement si renseigné
        if (!empty($password)) {
            $stmt = $db->prepare("UPDATE utilisateurs SET mot_de_passe = ? WHERE id = ?");
            $stmt->execute([password_hash($password, PASSWORD_DEFAULT), $id]);
        }

        header('Location: liste.php');
        exit;
    }

    // Pré-remplir depuis POST si erreur
    $utilisateur = array_merge($utilisateur, $_POST);
}
?>

<?php foreach ($erreurs as $err): ?>
<p class="error"><?= htmlspecialchars($err) ?></p>
<?php endforeach; ?>

<h2>Modifier un utilisateur</h2>

<form action="modifier.php?id=<?= $id ?>" class="form-container" method="POST">
    <div class="form-group">
        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" required value="<?= htmlspecialchars($utilisateur['prenom']) ?>">
    </div>
    <div class="form-group">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required value="<?= htmlspecialchars($utilisateur['nom']) ?>">
    </div>
    <div class="form-group">
        <label for="login">Login :</label>
        <input type="text" id="login" name="login" required value="<?= htmlspecialchars($utilisateur['login']) ?>">
    </div>
    <div class="form-group">
        <label for="password">Mot de passe (laisser vide pour ne pas changer) :</label>
        <input type="password" id="password" name="password">
    </div>
    <div class="form-group">
        <label for="role">Rôle :</label>
        <select id="role" name="role" required>
            <option value="">Sélectionnez un rôle</option>
            <option value="editeur" <?= $utilisateur['role'] === 'editeur' ? 'selected' : '' ?>>Éditeur</option>
            <option value="administrateur" <?= $utilisateur['role'] === 'administrateur' ? 'selected' : '' ?>>Administrateur</option>
        </select>
    </div>
    <div class="form-group">
        <button type="submit" class="btn btn-edit">Enregistrer</button>
        <a href="liste.php">Annuler</a>
    </div>
</form>

</body>
</html>