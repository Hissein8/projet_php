<?php
require_once '../entete.php';
require_once '../menu.php';
require_once '../db.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = htmlspecialchars($_POST['nom']);

    if (!empty($nom)) {

        $stmt2 = $db->prepare("SELECT id FROM categories WHERE nom = ?");
        $stmt2->execute([$nom]);

        $existingCategory = $stmt2->fetch(PDO::FETCH_ASSOC);
        if ($existingCategory) {
            echo "<p class='error'>Une catégorie avec ce nom existe déjà.</p>";
            exit();
        }

        $stmt = $db->prepare("INSERT INTO categories (nom) VALUES (?)");
        $stmt->execute([$nom]);

        header('Location: lister.php');
        exit();
    } else {
        echo "<p class='error'>Le nom de la catégorie ne peut pas être vide.</p>";
    }
}

?>


<h2>Ajouter une nouvelle catégorie</h2>
<form action="" method="POST" class="form-container">
    <div class="form-group">
        <label for="nom">Nom de la catégorie :</label>
        <input type="text" id="nom" name="nom" required placeholder="Entrez le nom de la catégorie">
    </div>
    <button type="submit" class="btn-new">
        <i class="fa-solid fa-plus">Ajouter</i> 
    </button>
</form>

</body>
</html>