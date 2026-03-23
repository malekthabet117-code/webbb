<?php require("config.php");?>

<!DOCTYPE html>
<html>
<head>
    <title>Historique</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Historique</h1>

    <table>
        <tr>
            <th>Livre</th>
            <th>Date Emprunt</th>
            <th>Date Retour</th>
        </tr>

        <?php
        $sql = "SELECT livres.titre, emprunts.date_emprunt, emprunts.date_retour
                FROM emprunts
                JOIN livres ON emprunts.livre_id = livres.id";

        $result = $conn->query($sql);

        while($row = $result->fetch_assoc()){
            echo "<tr>
                    <td>{$row['titre']}</td>
                    <td>{$row['date_emprunt']}</td>
                    <td>{$row['date_retour']}</td>
                  </tr>";
        }
        ?>
    </table>
</div>

</body>
</html>