<?php 
require("config.php"); 

$id = $_GET['id'];
session_start();
$user_id = $_SESSION['user_id'];

if(isset($_POST['emprunter'])){
    $sql = "INSERT INTO emprunts (utilisateur_id, livre_id, date_emprunt)
            VALUES ($user_id, $id, NOW())";

    $conn->query($sql);

    // rendre livre indisponible
    $conn->query("UPDATE livres SET disponible = FALSE WHERE id = $id");

    echo "<p style='color:green;'>Livre emprunté !</p>";
}

$sql = "SELECT * FROM livres WHERE id = $id";
$result = $conn->query($sql);
$livre = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Fiche Livre</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Fiche Livre</h1>

    <div class="card">
        <h2><?php echo $livre['titre']; ?></h2>
        <p>Auteur: <?php echo $livre['auteur']; ?></p>
        <p>Description: <?php echo $livre['description']; ?></p>
        <p>Statut: <?php echo $livre['disponible'] ? "Disponible" : "Indisponible"; ?></p>

        <?php if($livre['disponible']): ?>
        <form method="post">
            <button name="emprunter" class="btn">Emprunter</button>
        </form>
        <?php else: ?>
            <p style="color:red;">Non disponible</p>
        <?php endif; ?>

    </div>
</div>

</body>
</html>