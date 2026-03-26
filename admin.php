<?php
session_start();
require("config.php");

// 🔒 protection admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    echo "Accès refusé ❌";
    exit();
}

// ✅ valider
if(isset($_POST['valider'])){
    $user_id = intval($_POST['user_id']);
    $livre_id = intval($_POST['livre_id']);

    $conn->query("UPDATE emprunts 
                  SET status='valide' 
                  WHERE utilisateur_id=$user_id AND livre_id=$livre_id");

    $conn->query("UPDATE livres 
                  SET disponible=FALSE 
                  WHERE id=$livre_id");

    header("Location: admin.php");
    exit();
}

// ❌ refuser
if(isset($_POST['refuser'])){
    $user_id = intval($_POST['user_id']);
    $livre_id = intval($_POST['livre_id']);

    $conn->query("UPDATE emprunts 
                  SET status='refuse' 
                  WHERE utilisateur_id=$user_id AND livre_id=$livre_id");

    header("Location: admin.php");
    exit();
}

// 📥 afficher demandes
$sql = "SELECT emprunts.utilisateur_id, emprunts.livre_id, livres.titre, utilisateurs.nom
        FROM emprunts
        JOIN livres ON emprunts.livre_id = livres.id
        JOIN utilisateurs ON emprunts.utilisateur_id = utilisateurs.id
        WHERE emprunts.status='en_attente'";

$result = $conn->query($sql);

if(!$result){
    die("Erreur SQL: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Admin - Demandes</title>

<style>
body {
    font-family: Arial;
    background: #f5f5f5;
    text-align: center;
}

h1 {
    margin-top: 20px;
}

.card {
    background: white;
    padding: 15px;
    margin: 15px auto;
    width: 320px;
    border-radius: 10px;
    box-shadow: 0 0 10px #ccc;
}

button {
    padding: 8px 12px;
    margin: 5px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
}

.valider {
    background: green;
    color: white;
}

.refuser {
    background: red;
    color: white;
}
</style>
</head>

<body>

<h1>📋 Demandes en attente</h1>

<?php
if($result->num_rows > 0){
    while($row = $result->fetch_assoc()){
?>

    <div class="card">
        <p><strong>Livre:</strong> <?php echo $row['titre']; ?></p>
        <p><strong>User:</strong> <?php echo $row['nom']; ?></p>

        <form method="post">
            <input type="hidden" name="user_id" value="<?php echo $row['utilisateur_id']; ?>">
            <input type="hidden" name="livre_id" value="<?php echo $row['livre_id']; ?>">

            <button class="valider" name="valider">✅ Valider</button>
            <button class="refuser" name="refuser">❌ Refuser</button>
        </form>
    </div>

<?php
    }
} else {
    echo "<p>Aucune demande en attente</p>";
}
?>

</body>
</html>