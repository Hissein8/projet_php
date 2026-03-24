
<?php
require_once 'db.php';
require_once 'entete.php';
require_once 'menu.php';

$categories = $db->query("SELECT * FROM categories ORDER BY nom ASC")->fetchAll(PDO::FETCH_ASSOC);
$articles = $db->query("
    SELECT a.id, a.titre, a.description_courte, a.date_publication,
           c.nom AS categorie,
           CONCAT(u.prenom, ' ', u.nom) AS auteur,
            a.image_url
    FROM articles a
    JOIN categories c ON a.categorie_id = c.id
    JOIN utilisateurs u ON a.editeur_id = u.id
    ORDER BY a.date_publication DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>




<!-- liste des catégories pour le menu de filtrage -->
<div class="theme-filters">
    <button class='theme-btn' data-theme='all'>Tous</button>
    <?php foreach ($categories as $cat) { ?>
        <button class='theme-btn' data-theme='<?php echo htmlspecialchars($cat['nom']); ?>'><?php echo htmlspecialchars($cat['nom']); ?></button>
    <?php } ?>
</div>



<script>

    document.addEventListener('DOMContentLoaded', function() {
        const themeButtons = document.querySelectorAll('.theme-btn');
        themeButtons.forEach(button => {
            button.addEventListener('click', function() {
                themeButtons.forEach(btn => btn.classList.remove('active'));
                this.classList.add('active');
                const theme = this.getAttribute('data-theme');
                filterArticles(theme);
                // const theme = this.getAttribute('data-theme');
                // filterArticles(theme);
            });
        });
    });

    function filterArticles(theme) {
        const articles = document.querySelectorAll('.article');
        articles.forEach(article => {
            if (theme === 'all' || article.getAttribute('data-category') === theme) {
                article.style.display = 'block';
            } else {
                article.style.display = 'none';
            }
        });
    }
</script>



<section class='articles-container'><?php 
foreach ($articles as $art) {
    echo "<article class='article' data-category='" . htmlspecialchars($art['categorie']) . "'>";
    if ($art['image_url']) {
        echo "<img src='" . htmlspecialchars($art['image_url']) . "' alt='" . htmlspecialchars($art['titre']) . "' class='article-image'>";
    }
    echo "<h3><a href='articles/detail.php?id=" . (int)$art['id'] . "'>" . htmlspecialchars($art['titre']) . "</a></h3>";
    echo "<p class='category-badge'>" . htmlspecialchars($art['categorie']) . "</p>";
    echo "<p>" . nl2br(htmlspecialchars($art['description_courte'])) . "</p>";
    echo "<small>Publié le " . date('d/m/Y', strtotime($art['date_publication'])) . " par " . htmlspecialchars($art['auteur']) . "</small>";
    echo "</article>";
}

?></section>



<?php
// if (isset($_GET['categorie'])) {
//     $categorie = $_GET['categorie'];
//     echo "<h2>" . htmlspecialchars($categorie) . "</h2>";
//     echo "<div class='articles-container'>";
//     foreach ($articles as $art) {
//         if ($art['categorie'] === $categorie) {
//             echo "<article class='article'>";
//             if ($art['image_url']) {
//                 echo "<img src='" . htmlspecialchars($art['image_url']) . "' alt='" . htmlspecialchars($art['titre']) . "' class='article-image'>";
//             }
//             echo "<h3><a href='articles/detail.php?id=" . (int)$art['id'] . "'>" . htmlspecialchars($art['titre']) . "</a></h3>";
//             echo "<p class='category-badge'>" . htmlspecialchars($art['categorie']) . "</p>";
//             echo "<p>" . nl2br(htmlspecialchars($art['description_courte'])) . "</p>";
//             echo "<small>Publié le " . date('d/m/Y', strtotime($art['date_publication'])) . " par " . htmlspecialchars($art['auteur']) . "</small>";
//             echo "</article>";
//         }
//     }
//     echo "</div>";
// } else {

//     foreach ($categories as $cat) {
//         echo "<h2>" . htmlspecialchars($cat['nom']) . "</h2>";
//         echo "<div class='articles-container'>";
//         foreach ($articles as $art) {
//             if ($art['categorie'] === $cat['nom']) {
//                 echo "<article class='article'>";
//                 if ($art['image_url']) {
//                    echo "<img src='" . htmlspecialchars($art['image_url']) . "' alt='" . htmlspecialchars($art['titre']) . "' class='article-image'>";
//                 }
//                 echo "<h3><a href='articles/detail.php?id=" . (int)$art['id'] . "'>" . htmlspecialchars($art['titre']) . "</a></h3>";
//                 echo "<p class='category-badge'>" . htmlspecialchars($art['categorie']) . "</p>";
//                 echo "<p>" . nl2br(htmlspecialchars($art['description_courte'])) . "</p>";
//                 echo "<small>Publié le " . date('d/m/Y', strtotime($art['date_publication'])) . " par " . htmlspecialchars($art['auteur']) . "</small>";
//                 echo "</article>";
//             }
//         }
//         echo "</div>";
//     }
// }

// foreach ($categories as $cat) {
//     echo "<h2>" . htmlspecialchars($cat['nom']) . "</h2>";
//     echo "<div class='articles-container'>";
//     foreach ($articles as $art) {
//         if ($art['categorie'] === $cat['nom']) {
//             echo "<article class='article'>";
//             if ($art['image_url']) {
//                 echo "<img src='" . htmlspecialchars($art['image_url']) . "' alt='" . htmlspecialchars($art['titre']) . "' class='article-image'>";
//             }
//             echo "<h3><a href='articles/detail.php?id=" . (int)$art['id'] . "'>" . htmlspecialchars($art['titre']) . "</a></h3>";
//             echo "<p class='category-badge'>" . htmlspecialchars($art['categorie']) . "</p>";
//             echo "<p>" . nl2br(htmlspecialchars($art['description_courte'])) . "</p>";
//             echo "<small>Publié le " . date('d/m/Y', strtotime($art['date_publication'])) . " par " . htmlspecialchars($art['auteur']) . "</small>";
//             echo "</article>";
//         }
//     }
//     echo "</div>";
// }
?>