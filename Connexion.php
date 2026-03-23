<?php 
session_start();
require("config.php");
?>

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
            <h1 id="id2">BookNomad</h1>
        </div>
    <nav>
        <a href="index.html">Accueil</a>
        <a href="catalogue.html">Catalogue</a>
        <a href="Inscription.html">Inscription</a>
    </nav>
</header>

<div class="container" >

    <div class="left">
        <h1 id="id1">Connectez-vous</h1>
        <p>
            accédez instantanément à votre bibliothèque numérique personnelle. Retrouvez vos livres favoris, explorez de nouvelles lectures et gérez vos emprunts en toute simplicité.
        </p><br>
        <img src="cata.jpg"  height="274px">

</div>

    <div class="right">
        <form>
            <h2>Connexion à votre compte </h2>
             <?php
    if(isset($_POST['login'])){
        $email = $_POST['email'];
        $pass = $_POST['password'];

        $sql = "SELECT * FROM utilisateurs WHERE email='$email' AND mot_de_passe='$pass'";
        $result = $conn->query($sql);

        if($result->num_rows > 0){
            $user = $result->fetch_assoc();

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nom'] = $user['nom'];

            header("Location: catalogue.php");
        } else {
            echo "<p style='color:red;'>Email ou mot de passe incorrect</p>";
        }
    }
            <input type="email" placeholder="Email">
            <input type="password" placeholder="Mot de passe">
            <button type="submit">Se Connecter</button>
            <p class="login">
                Pas encore un compte ? <a href="Inscription.html">S'inscrire</a>
            </p>
        </form>
    </div>
</div>
</body>
</html>
