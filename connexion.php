<?php
require_once 'db.php';
require_once 'entete.php';


function authentifier($nomUtilisateur, $password, $role) {
    global $db;
    $stmt = $db->prepare("SELECT * FROM utilisateurs WHERE login = ? AND role = ?");
    $stmt->execute([$nomUtilisateur, $role]);
    $user = $stmt->fetch();
    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}


if (isset($_SESSION['user']) && ($_SESSION['user']['role'] === 'editeur' || $_SESSION['user']['role'] === 'administrateur')) {
    
    echo "<p class='success'>Vous êtes déjà connecté en tant que " . htmlspecialchars($_SESSION['user']['login']) . " (" . htmlspecialchars($_SESSION['user']['role']) . ").</p>";
    echo "<p><a href='deconnexion.php' class='btn'>Se déconnecter</a></p>";
    header('Location: accueil.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomUtilisateur = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['usertype'];
    $user = authentifier($nomUtilisateur, $password, $role);
    if ($user) {
        $_SESSION['user'] = $user;
        header('Location: accueil.php');
        exit();
    } else {
        echo "<p class='error'>Nom d'utilisateur, mot de passe ou type d'utilisateur incorrect.</p>";
    }
}
?>

<!-- accès au type d'utilisateur $_SESSION['user']['role'] -->

    <div class="login-container">
        <form action="" method="POST" class="login-form">
            <fieldset>
                <legend><i class="fa-solid fa-lock"></i> Connexion</legend>

                <div class="form-group">
                    <!-- <label for="usertype"><i class="fa-solid fa-user-tag"></i> Type d'utilisateur :</label> -->
                    <label for="usertype"> Type d'utilisateur :</label>
                    <select name="usertype" id="usertype" required>
                        <option value="">Sélectionnez un type</option>
                        <!-- <option value="visiteur">Visiteur</option> -->
                        <option value="editeur">Éditeur</option>
                        <option value="administrateur">Administrateur</option>
                    </select>
                </div>


                <!-- pas besoin de prénom pour la connexion, on peut le supprimer du formulaire et de la validation -->
                
                <!-- <div class="form-group"> -->
                    <!-- <label for="username"><i class="fa-solid fa-user"></i> Nom d'utilisateur :</label> -->
                    <!-- <label for="firstname"> Prénom :</label> -->
                    <!-- <input type="text" id="firstname" name="firstname" required placeholder="Entrez votre prénom"> -->
                <!-- </div> -->


                <!-- pas besoin de nom pour la connexion, on peut le supprimer du formulaire et de la validation --> 
                <!-- <div class="form-group"> -->
                    <!-- <label for="username"><i class="fa-solid fa-user"></i> Nom d'utilisateur :</label> -->
                    <!-- <label for="lastname"> Nom :</label> -->
                    <!-- <input type="text" id="lastname" name="lastname" required placeholder="Entrez votre nom"> -->
                <!-- </div> -->


                <div class="form-group">
                    <!-- <label for="username"><i class="fa-solid fa-user"></i> Nom d'utilisateur :</label> -->
                    <label for="username"> Nom d'utilisateur :</label>
                    <input type="text" id="username" name="username" required placeholder="Entrez votre nom d'utilisateur">
                </div>

                <div class="form-group">
                    <!-- <label for="password"><i class="fa-solid fa-key"></i> Mot de passe :</label> -->
                    <label for="password"> Mot de passe :</label>
                    <input type="password" id="password" name="password" required placeholder="Entrez votre mot de passe">
                </div>

                <button type="submit" class="login-btn">
                    <i class="fa-solid fa-sign-in-alt"></i> Se connecter
                </button>
            </fieldset>
        </form>
    </div>
    <script src="/script.js"></script>

    <script>
        // Validation du formulaire de connexion
        document.querySelector('.login-form').addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;

            let isValid = true;
            let errorMessage = '';

            // Validation du nom d'utilisateur (au moins 2 caractères)
            if (username.length < 2) {
                isValid = false;
                errorMessage += 'Le nom d\'utilisateur doit contenir au moins 2 caractères.\n';
                document.getElementById('username').style.borderColor = '#dc3545';
            } else {
                document.getElementById('username').style.borderColor = '#28a745';
            }

            // Validation du mot de passe (au moins 3 caractères)
            if (password.length < 3) {
                isValid = false;
                errorMessage += 'Le mot de passe doit contenir au moins 3 caractères.\n';
                document.getElementById('password').style.borderColor = '#dc3545';
            } else {
                document.getElementById('password').style.borderColor = '#28a745';
            }

            // Si validation échoue, empêcher la soumission et afficher l'erreur
            if (!isValid) {
                e.preventDefault();
                alert(errorMessage);
            }
        });

        // Réinitialiser la couleur des bordures lors de la saisie
        document.getElementById('username').addEventListener('input', function() {
            this.style.borderColor = '#ddd';
        });

        document.getElementById('password').addEventListener('input', function() {
            this.style.borderColor = '#ddd';
        });
    </script>

</body>
</html>