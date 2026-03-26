<?php 
require("config.php");
session_start();

if(!isset($_SESSION['user_id'])){
    header("Location: Connexion.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$id = $_GET['id'];

// récupérer livre
$sql = "SELECT * FROM livres WHERE id = $id";
$result = $conn->query($sql);
$livre = $result->fetch_assoc();

// envoyer demande
if(isset($_POST['emprunter'])){

    $sql = "INSERT INTO emprunts (utilisateur_id, livre_id, date_emprunt)
            VALUES ($user_id, $id, NOW())";

    if($conn->query($sql)){
        echo "<p style='color:orange;'>Demande envoyée ! </p>";
    } else {
        echo "Erreur: " . $conn->error;
    }
}
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
    <button name="emprunter">Envoyer une demande</button>
</form>
<?php else: ?>
<p>Indisponible</p>
<?php endif; ?>
    </div>
    <a href="catalogue.php" style="text-decoration:none;">
    <button type="button" style="background:gray; color:white;">
        ⬅ Retour
    </button>
</a>
</div>

</body>
</html>