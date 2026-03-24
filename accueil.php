<?php
require_once 'entete.php';
require_once 'menu.php';
require_once 'db.php';

?>

    <div class="themes-container">
        <button class="theme-btn active" data-theme="all">Tous</button>
        <button class="theme-btn" data-theme="sport">Sport</button>
        <button class="theme-btn" data-theme="sante">Santé</button>
        <button class="theme-btn" data-theme="culture">Culture</button>
        <button class="theme-btn" data-theme="technologie">Technologie</button>
        <button class="theme-btn" data-theme="conflit">Conflits</button>
        <button class="theme-btn" data-theme="politique">Politique</button>
        <button class="theme-btn" data-theme="Éducation">Education</button>

    </div>

    <!-- Articles
        <section class="articles-container">
        <article class="article" data-category="sport">
            <h3>Football : France champion d'Europe</h3>
            <p class="category-badge">Sport</p>
            <p>L'équipe de France remporte un succès éclatant lors du dernier match...</p>
            <small>Publié le 15 mars 2026</small>
        </article>

        <article class="article" data-category="sante">
            <h3>Nouvelle avancée dans le traitement du diabète</h3>
            <p class="category-badge">Santé</p>
            <p>Les chercheurs découvrent une molécule révolutionnaire pour traiter le diabète...</p>
            <small>Publié le 14 mars 2026</small>
        </article>

        <article class="article" data-category="culture">
            <h3>Exposition Picasso au Louvre</h3>
            <p class="category-badge">Culture</p>
            <p>Une nouvelle exposition temporaire présente les chefs-d'œuvre du maître cubiste...</p>
            <small>Publié le 13 mars 2026</small>
        </article>

        <article class="article" data-category="technologie">
            <h3>Intelligence Artificielle : révolution ou danger ?</h3>
            <p class="category-badge">Technologie</p>
            <p>Un débat sur l'impact de l'IA dans notre société contemporaine...</p>
            <small>Publié le 12 mars 2026</small>
        </article>

        <article class="article" data-category="sport">
            <h3>Tennis : Djokovic se retire</h3>
            <p class="category-badge">Sport</p>
            <p>Le champion annonce son retrait de la compétition professionnelle...</p>
            <small>Publié le 11 mars 2026</small>
        </article>

        <article class="article" data-category="sante">
            <h3>Dangers du tabac : une étude alarmante</h3>
            <p class="category-badge">Santé</p>
            <p>Selon une nouvelle étude, les risques du tabagisme seraient plus graves que prévus...</p>
            <small>Publié le 10 mars 2026</small>
        </article>

    </section> -->

    <!-- Les articles seront chargés dynamiquement via JavaScript  et recuperer depuis la base de données -->
    <?php
    $sql = "SELECT a.id, a.titre, a.description_courte, a.contenu, a.image_url, a.date_publication,
            c.nom AS categorie,
            CONCAT(u.prenom, ' ', u.nom) AS auteur
        FROM articles a
        JOIN categories c ON a.categorie_id = c.id
        JOIN utilisateurs u ON a.editeur_id = u.id
        ORDER BY a.date_publication DESC";
    $articles = $db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    foreach ($articles as $article) {
        // Affichage de chaque article
        // Utiliser htmlspecialchars pour éviter les problèmes de sécurité XSS
        echo '<article class="article" data-category="' . htmlspecialchars($article['categorie']) . '">';
        if ($article['image_url']) {
            echo '<img src="' . htmlspecialchars($article['image_url']) . '" alt="' . htmlspecialchars($article['titre']) . '">';
        }
        echo '<h3>' . htmlspecialchars($article['titre']) . '</h3>';
        echo '<p class="category-badge">' . htmlspecialchars($article['categorie']) . '</p>';
        echo '<p>' . nl2br(htmlspecialchars($article['description_courte'])) . '</p>';
        echo '<small>Publié le ' . date('d/m/Y', strtotime($article['date_publication'])) . '</small>';
        echo '</article>';
    }
    ?>

    <script src="script.js"></script>
</body>
</html>