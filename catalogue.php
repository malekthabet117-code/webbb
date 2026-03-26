<?php require("config.php");?>

<!DOCTYPE html>
<html>
<head>
    <title>Catalogue</title>
    <link rel="stylesheet" href="styleCatalogue.css">
</head>
<body>

<div class="container">
    <h1>Liste des Livres</h1>

    <div class="grid">
    <?php
    $sql = "SELECT * FROM livres";
    $result = $conn->query($sql);
    while($row = $result->fetch_assoc()){
        echo "
        <div class='card'>
            <h3>{$row['titre']}</h3>
            <p>{$row['auteur']}</p>

            <a href='ficheLivre.php?id={$row['id']}' class='btn'>Voir</a>
        </div>
        ";
    }
    ?><br>
    <a href="mesEmprunts.php">Mes emprunts</a> 
    <a href="ajouterLivre.php">Ajouter livre</a> 
    <a href="historique.php">Historique</a> 
    </div>
</div>

</body>
</html>