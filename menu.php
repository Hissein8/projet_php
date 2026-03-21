<?php
// Pas besoin de session_start() ici, il est déjà appelé dans entete.php
?>

<nav>

    <!-- Accueil toujours visible -->
    <div><i class="fa-solid fa-house"></i> <a href="<?php echo $basePath; ?>/accueil.php" class="nav-link">Home</a></div>

<!-- Menu d'administration (visible seulement pour éditeurs et admins) -->
<?php if (isset($_SESSION['role']) &&
          ($_SESSION['role'] === 'editeur' || $_SESSION['role'] === 'administrateur')) : ?>
<div class="admin-menu">
    <div class="admin-links">
        <a href="<?php echo $basePath; ?>/articles/ajouter.php" class="admin-link">
            <i class="fa-solid fa-plus"></i> Ajouter un article
        </a>
        <a href="<?php echo $basePath; ?>/articles/liste.php" class="admin-link">
            <i class="fa-solid fa-list"></i> Gérer les articles
        </a>
        <a href="<?php echo $basePath; ?>/categories/liste.php" class="admin-link">
            <i class="fa-solid fa-tags"></i> Gérer les catégories
        </a>
        <?php if ($_SESSION['role'] === 'administrateur') : ?>
        <a href="<?php echo $basePath; ?>/utilisateurs/liste.php" class="admin-link">
            <i class="fa-solid fa-users"></i> Gérer les utilisateurs
        </a>
        <?php endif; ?>
    </div>
</div>
<?php endif; ?>

<hr>

<script src="<?php echo $basePath; ?>/script.js"></script>