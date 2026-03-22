<?php
// Pas besoin de session_start() ici, il est déjà appelé dans entete.php
?>

<nav>
    <!-- Accueil toujours visible -->
    <div><i class="fa-solid fa-house"></i> <a href="index.php" class="nav-link">Home</a></div>

    <!-- Menu d'administration (visible seulement pour éditeurs et admins) -->
    <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'editeur') : ?>
        <div class="admin-menu">
            <div class="admin-links">
                <a href="articles/ajouter.php" class="admin-link">
                    <i class="fa-solid fa-plus"></i> Ajouter un article
                </a>
                <a href="articles/liste.php" class="admin-link">
                    <i class="fa-solid fa-list"></i> Gérer les articles
                </a>
                <a href="categories/liste.php" class="admin-link">
                    <i class="fa-solid fa-tags"></i> Gérer les catégories
                </a>
            </div>
        </div>
    <?php elseif (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'administrateur') : ?>
        <div class="admin-menu">
            <div class="admin-links">
                <a href="articles/ajouter.php" class="admin-link">
                    <i class="fa-solid fa-plus"></i> Ajouter un article
                </a>
                <a href="articles/liste.php" class="admin-link">
                    <i class="fa-solid fa-list"></i> Gérer les articles
                </a>
                <a href="categories/liste.php" class="admin-link">
                    <i class="fa-solid fa-tags"></i> Gérer les catégories
                </a>
                <a href="utilisateurs/liste.php" class="admin-link">
                    <i class="fa-solid fa-users"></i> Gérer les utilisateurs
                </a>
            </div>
        </div>
    <?php endif; ?>
</nav>