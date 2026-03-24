<?php require("config.php"); ?>

<!DOCTYPE html>
<html>
<head>
    <title>Catalogue</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Catalogue des Livres</h1>

    <div class="grid">
    <?php
    $sql = "SELECT * FROM livres";
    $result = $conn->query($sql);

    while($row = $result->fetch_assoc()){
        echo "
        <div class='card'>
            <h3>{$row['titre']}</h3>
            <p>{$row['auteur']}</p>

            <a href='fiche-livre.php?id={$row['id']}' class='btn'>Voir</a>
        </div>
        ";
    }
    ?>
    <a href="mesEmprunts.php">Mes emprunts</a> 
    <a href="ajouterLivre.php">Ajouter livre</a> 
    <a href="ficheLivre.php">Fiche livre</a> |
    <a href="historique.php">Historique</a> 
    </div>
</div>

</body>
</html>