<?php require("config.php"); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Ajouter Livre</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Ajouter un Livre</h1>

    <?php
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $titre = $_POST["titre"];
        $auteur = $_POST["auteur"];
        $description = $_POST["description"];

        $sql = "INSERT INTO livres (titre, auteur, description, disponible)
                VALUES ('$titre', '$auteur', '$description', TRUE)";

        if($conn->query($sql)){
            echo "<p style='color:green;'>Livre ajouté !</p>";
        } else {
            echo "Erreur: " . $conn->error;
        }
    }
    ?>

    <form method="post">
        <input type="text" name="titre" placeholder="Titre" required>
        <input type="text" name="auteur" placeholder="Auteur" required>
        <textarea name="description" placeholder="Description"></textarea>
        <button class="btn">Ajouter</button>
    </form>
</div>

</body>
</html>