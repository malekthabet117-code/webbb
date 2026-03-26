<?php
session_start();
require("config.php");

$user_id = $_SESSION['user_id'];
$nom = $_SESSION['nom'];

// récupérer demandes avec titre livre
$sql = "SELECT livres.titre, emprunts.status 
        FROM emprunts 
        JOIN livres ON emprunts.livre_id = livres.id 
        WHERE emprunts.utilisateur_id = $user_id";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <style>
        body {
            font-family: Arial;
            background: #f4f4f4;
            text-align: center;
        }

        h1 {
            margin-top: 20px;
        }

        .card {
            background: white;
            width: 300px;
            margin: 15px auto;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 0 10px #ccc;
        }

        .btn {
            display: inline-block;
            margin: 10px;
            padding: 10px 15px;
            background: #333;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .orange { color: orange; }
        .green { color: green; }
        .red { color: red; }
    </style>
</head>

<body>

<h1>📊 Tableau de bord</h1>
<h2>Bienvenue <?php echo $nom; ?> 👋</h2>

<a href="catalogue.php" class="btn">📚 Catalogue</a>
<a href="logout.php" class="btn">🚪 Logout</a>

<h3>📖 Mes demandes</h3>

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){

        $class = "";
        if($row['status'] == 'en_attente') $class = "orange";
        if($row['status'] == 'valide') $class = "green";
        if($row['status'] == 'refuse') $class = "red";
?>

    <div class="card">
        <p><strong>Livre:</strong> <?php echo $row['titre']; ?></p>
        <p class="<?php echo $class; ?>">
            <strong>Statut:</strong> <?php echo $row['status']; ?>
        </p>
    </div>

<?php
    }
} else {
    echo "<p>Aucune demande pour le moment</p>";
}
?>

</body>
</html>