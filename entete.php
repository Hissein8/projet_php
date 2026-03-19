<?php
session_start();

// Déterminer le chemin de base du projet
$basePath = '/projet_php';
if (strpos($_SERVER['REQUEST_URI'], '/projet_php') === false) {
    $basePath = '';
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon site d'actualité</title>
    <link rel="stylesheet" href="<?php echo $basePath; ?>/style.css">
    <script src="https://kit.fontawesome.com/61523b4b4d.js"></script>
</head>
<body>
    
        <header>
            <div><i class="fa-solid fa-newspaper"> News </i></div>

            <div class="menu-container">
                <?php include dirname(__FILE__) . '/menu.php'; ?>
            </div>

            <div><i class="fa-regular fa-user"></i> 
            <?php if (isset($_SESSION['username'])) : ?> <span> Bienvenue, <strong> <?= htmlspecialchars($_SESSION['username']) ?> </strong> ! </span> <button><a class="link" href="<?php echo $basePath; ?>/deconnexion.php"> Se déconnecter </a></button> <?php
            else : ?> <button><a class="link" href="<?php echo $basePath; ?>/connexion.php"> Se connecter </a></button> <?php endif; ?>
            </div>
        </header>
        <hr>
        <script src="<?php echo $basePath; ?>/script.js"></script>