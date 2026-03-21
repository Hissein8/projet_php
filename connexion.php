<?php
// =============================================
// TRAITEMENT PHP EN PREMIER (avant tout HTML)
// =============================================
require_once 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM utilisateurs WHERE login = :username";
    $stmt = $db->prepare($sql);
    $stmt->execute([':username' => $username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['username'] = $user['login'];
        $_SESSION['role']     = $user['role'];
        $_SESSION['id']       = $user['id'];
        header('Location: accueil.php');
        exit();
    } else {
        $error = "Identifiants incorrects.";
    }
}

// =============================================
// HTML APRES le traitement
// =============================================
require_once 'entete.php';
require_once 'menu.php';
?>

<div class="login-container">

    <?php if (isset($error)) : ?>
        <p style="color:red"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <form action="" method="POST" class="login-form">
        <fieldset>
            <legend><i class="fa-solid fa-lock"></i> Connexion</legend>

            <div class="form-group">
                <label for="username">Nom d'utilisateur :</label>
                <input type="text" id="username" name="username" required
                    placeholder="Entrez votre nom d'utilisateur"
                    value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required
                    placeholder="Entrez votre mot de passe">
            </div>

            <button type="submit" class="login-btn">
                <i class="fa-solid fa-sign-in-alt"></i> Se connecter
            </button>
        </fieldset>
    </form>
</div>

<script>
    document.querySelector('.login-form').addEventListener('submit', function(e) {
        const username = document.getElementById('username').value.trim();
        const password = document.getElementById('password').value;
        let isValid = true;
        let errorMessage = '';

        if (username.length < 2) {
            isValid = false;
            errorMessage += 'Le nom d\'utilisateur doit contenir au moins 2 caractères.\n';
            document.getElementById('username').style.borderColor = '#dc3545';
        } else {
            document.getElementById('username').style.borderColor = '#28a745';
        }

        if (password.length < 3) {
            isValid = false;
            errorMessage += 'Le mot de passe doit contenir au moins 3 caractères.\n';
            document.getElementById('password').style.borderColor = '#dc3545';
        } else {
            document.getElementById('password').style.borderColor = '#28a745';
        }

        if (!isValid) {
            e.preventDefault();
            alert(errorMessage);
        }
    });

    document.getElementById('username').addEventListener('input', function() {
        this.style.borderColor = '#ddd';
    });
    document.getElementById('password').addEventListener('input', function() {
        this.style.borderColor = '#ddd';
    });
</script>