<?php
require_once 'entete.php';
require_once 'menu.php';
?>

<!-- <!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Site d'Actualités</title>
    <script src="https://kit.fontawesome.com/61523b4b4d.js"></script>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav>
        <div><i class="fa-solid fa-newspaper"> News </i></div>
        <div> <i class="fa-solid fa-house"> <a id="link" href="index.php"> Home </a> </i> </div>
        <div><i class="fa-regular fa-user"></i> <button><a id="link" href="connexion.php"> Se connecter </a></button></div>
    </nav> -->
    <!-- <nav>
        <div><i class="fa-solid fa-newspaper"></i> News</div>
        <div><i class="fa-solid fa-house"></i> <a href="index.php" style="color: inherit; text-decoration: none;">Home</a></div>
        <div><i class="fa-regular fa-user"></i> Connexion</div>
    </nav> -->

    <div class="login-container">
        <form action="" method="POST" class="login-form">
            <fieldset>
                <legend><i class="fa-solid fa-lock"></i> Connexion</legend>

                <div class="form-group">
                    <!-- <label for="usertype"><i class="fa-solid fa-user-tag"></i> Type d'utilisateur :</label> -->
                    <label for="usertype"> Type d'utilisateur :</label>
                    <select name="usertype" id="usertype" required>
                        <option value="">Sélectionnez un type</option>
                        <option value="visiteur">Visiteur</option>
                        <option value="editeur">Éditeur</option>
                        <option value="administrateur">Administrateur</option>
                    </select>
                </div>

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
    <script src="script.js"></script>

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