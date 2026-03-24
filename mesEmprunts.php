<?php require("config.php"); ?>

<?php
// retour livre
if(isset($_POST['retour'])){
    $id = $_POST['livre_id'];

    $conn->query("UPDATE emprunts SET date_retour = NOW() WHERE livre_id = $id AND date_retour IS NULL");

    $conn->query("UPDATE livres SET disponible = TRUE WHERE id = $id");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Mes Emprunts</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Mes Emprunts</h1>

    <table>
        <tr>
            <th>Livre</th>
            <th>Date</th>
            <th>Action</th>
        </tr>

        <?php
        $sql = "SELECT livres.id, livres.titre, emprunts.date_emprunt
                FROM emprunts
                JOIN livres ON emprunts.livre_id = livres.id
                WHERE emprunts.date_retour IS NULL";

        $result = $conn->query($sql);

        while($row = $result->fetch_assoc()){
            echo "
            <tr>
                <td>{$row['titre']}</td>
                <td>{$row['date_emprunt']}</td>
                <td>
                    <form method='post'>
                        <input type='hidden' name='livre_id' value='{$row['id']}'>
                        <button name='retour' class='btn'>Retourner</button>
                    </form>
                </td>
            </tr>
            ";
        }
        ?>
    </table>
</div>

</body>
</html>