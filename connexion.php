<?php
require_once 'db.php';
require_once 'entete.php';


    function authentifier($nomUtilisateur, $password) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE login = ?");
    $stmt->execute([$nomUtilisateur]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    // if ($user && ($password === $user['mot_de_passe'])) {
    if ($user && password_verify($password, $user['mot_de_passe'])) {
        return $user;
    }
    
    return false;
}


if (isset($_SESSION['user']) && in_array($_SESSION['user']['role'], ['editeur', 'administrateur'])) {
    header('Location: accueil.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomUtilisateur = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    // $role = $_POST['usertype'] ?? '';

    if (empty($nomUtilisateur) || empty($password)) {
        $error = "Tous les champs sont obligatoires.";
    } else {
        $user = authentifier($nomUtilisateur, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: accueil.php');
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
?>


    <div class="login-container">
        <form action="connexion.php" method="POST" class="login-form">
            <fieldset>
                <legend><i class="fa-solid fa-lock"></i> Connexion</legend>
                <div class="form-group">
                    <label for="username"> Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" required placeholder="Entrez votre nom d'utilisateur">
                </div>

                <div class="form-group">
                    <label for="password"> Mot de passe :</label>
                    <input type="password" id="password" name="password" required placeholder="Entrez votre mot de passe">
                </div>

                <?php if (!empty($error)): ?>
                    <div class="error-message" style="color:#dc3545; background:#fdf0f0; border:1px solid #dc3545; border-radius:4px; padding:10px; margin-bottom:12px;">
                        <i class="fa-solid fa-circle-exclamation"></i> <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                <button type="submit" class="login-btn">
                    <i class="fa-solid fa-sign-in-alt"></i> Se connecter
                </button>
            </fieldset>
        </form>
    </div>
    <script src="script.js"></script>

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

</body>
</html>