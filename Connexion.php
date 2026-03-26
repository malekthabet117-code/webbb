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
        <a href="index.php">Accueil</a>
        <a href="catalogue.php">Catalogue</a>
        <a href="Inscription.php">Inscription</a>
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
        <form method="POST">
            <h2>Connexion à votre compte </h2>
             <?php
if(isset($_POST['login'])){
    $email = $_POST['email'];
    $pass = $_POST['password'];

    // 🔐 sécurisé (prepared statement)
    $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE email=? AND password=?");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        $user = $result->fetch_assoc();

        // ✅ sessions
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nom'] = $user['nom'];
        $_SESSION['role'] = $user['role'];

        // 🔥 redirection حسب role
        if($user['role'] == 'admin'){
            header("Location: admin.php");
        } else {
            header("Location: catalogue.php");
        }
        exit();

    } else {
        echo "<p style='color:red;'>Email ou mot de passe incorrect ❌</p>";
    }
}
?>
            <input type="email" name="email" placeholder="Email">
            <input type="password" name="password" placeholder="Mot de passe">
            <button type="submit" name="login">Se connecter</button>
            <p class="login">
                Pas encore un compte ? <a href="Inscription.php">S'inscrire</a>
            </p>
        </form>
    </div>
</div>
</body>
</html>
