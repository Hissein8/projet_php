<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon site d'actualité</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://kit.fontawesome.com/61523b4b4d.js"></script>
</head>
<body>
    
        <header>
            <div><i class="fa-solid fa-newspaper"> News </i></div>
            <!-- <div> <i class="fa-solid fa-house"> <a id="link" href="accueil.php"> Home </a> </i> </div> -->
            <!-- <div><i class="fa-regular fa-user"></i> <button><a id="link" href="connexion.php"> Se connecter </a></button></div>-->



            <div class="menu-container">
                <?php include 'menu.php'; ?>
            </div>



            <div><i class="fa-regular fa-user"></i> 
            <?php if (isset($_SESSION['username'])) : ?> <span> Bienvenue, <strong> <?= htmlspecialchars($_SESSION['username']) ?> </strong> ! </span> <button><a id="link" href="deconnexion.php"> Se déconnecter </a></button> <?php
            else : ?> <button><a id="link" href="connexion.php"> Se connecter </a></button> <?php endif; ?>
            </div>
        </header>
        <hr>
        <script src="script.js"></script>
</html>