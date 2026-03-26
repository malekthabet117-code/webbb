<?php require("config.php"); ?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
    <link rel="stylesheet" href="styleInscription.css">
</head>

<body>

<header>
    <div class="logo">
            <img src="logo.png" width="10%" height="10%">
            <h1>BookNomad</h1>
        </div>
    <nav>
        <a href="index.php">Accueil</a>
        <a href="catalogue.php">Catalogue</a>
        <a href="Connexion.php">Connexion</a>
    </nav>
</header>

<div class="container" >

    <div class="left" >
        <h1>S'enregistrer</h1>
        <p>
            Créez votre compte gratuitement et commencez à emprunter
            des livres numériques dans notre bibliothèque en ligne.
        </p><br>
        <img src="bon.jpg" widh="100%" height="300px">

</div>

    <div class="right">
        <form method="POST">
            <h2>Créer un compte</h2>

            <?php
    if(isset($_POST['register'])){
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $role = $_POST['role'];

        $sql = "INSERT INTO utilisateurs (nom, prenom, email, password,role)
VALUES ('$nom', '$prenom', '$email', '$password','$role')";
echo "Inscription réussie ✅";
        if($conn->query($sql)){
    header("Location: catalogue.php");
    exit();
} else {
    echo "Erreur: " . $conn->error;
}
    }
    ?>
            
            <input type="text" name="nom" placeholder="Nom d'utilisateur">
            <input type="text" name="prenom" placeholder="Prenom d'utilisateur">
            <input type="email" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Mot de passe">
            <label>Rôle :</label><br>

<input type="radio" name="role" value="user" checked> User
<input type="radio" name="role" value="admin"> Admin
            <div class="checkbox">
                <input type="checkbox">J’accepte le règlement intérieur de la bibliothèque <br>
                <input type="checkbox">J’accepte les conditions d’emprunt et de retour


            </div>

            <button type="submit" name="register">S'inscrire</button><br>

            <p class="login">
                Déjà un compte ? <a href="Connexion.php">Se connecter</a>
            </p>
            
        </form>
    </div>

</div>

</body>
</html>
