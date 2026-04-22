<?php
session_start();
require("config.php");

if (isset($_SESSION['user_id'])) {
    header("Location: catalogue.php"); exit();
}

$erreur = "";
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);
    $stmt  = $conn->prepare("SELECT * FROM utilisateurs WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['nom']     = $user['nom'];
        $_SESSION['prenom']  = $user['prenom'];
        $_SESSION['role']    = $user['role'];
        header("Location: " . ($user['role'] === 'admin' ? "admin.php" : "catalogue.php"));
        exit();
    } else {
        $erreur = "Email ou mot de passe incorrect.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion – BookNomad</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container {
            display: flex;
            min-height: calc(100vh - 60px);
        }

        .auth-left {
            flex: 1;
            background: linear-gradient(135deg, #3e2c1c, #5c4033);
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 80px 60px;
            color: #f5e6d0;
            position: relative;
            overflow: hidden;
        }

        .auth-left::before {
            position: absolute;
            right: -10px;
            bottom: 20px;
            font-size: 180px;
            opacity: 0.08;
        }

        .auth-left h1 {
            font-family: Cambria, Georgia, serif;
            font-size: 2.2rem;
            color: #edb55a;
            margin-bottom: 16px;
        }

        .auth-left p {
            font-size: 1rem;
            line-height: 1.7;
            color: #d4b896;
            max-width: 400px;
        }

        .auth-left img {
            margin-top: 30px;
            border-radius: 12px;
            max-width: 100%;
            max-height: 240px;
            object-fit: cover;
            opacity: 0.85;
        }

        .auth-right {
            flex: 1;
            background: #fffaf0;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px;
        }

        .auth-form {
            width: 100%;
            max-width: 380px;
        }

        .auth-form h2 {
            font-family: Cambria, Georgia, serif;
            font-size: 1.7rem;
            color: #3e2c1c;
            margin-bottom: 6px;
        }

        .auth-form .subtitle {
            font-size: 0.88rem;
            color: #9a7a5a;
            margin-bottom: 28px;
        }

        .auth-form input {
            width: 100%;
            padding: 12px 16px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: 1px solid #d7c2a5;
            background: #faf4e6;
            font-family: Cambria, Georgia, serif;
            font-size: 14px;
            color: #3e2c1c;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .auth-form input:focus {
            outline: none;
            border-color: #b08968;
            box-shadow: 0 0 8px rgba(176,137,104,0.35);
        }

        .auth-form button {
            width: 100%;
            padding: 13px;
            background: #5c4033;
            color: #fff8ee;
            border: none;
            border-radius: 8px;
            font-family: Cambria, Georgia, serif;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s, transform 0.15s;
            margin-top: 4px;
        }

        .auth-form button:hover { background: #3b2a1a; transform: translateY(-2px); }

        .auth-form .link-line {
            text-align: center;
            margin-top: 18px;
            font-size: 14px;
            color: #7a6a55;
        }

        .auth-form .link-line a {
            color: #b08968;
            font-weight: 700;
            text-decoration: none;
        }

        .auth-form .link-line a:hover { text-decoration: underline; }

        .error-msg {
            background: #f8d7da; color: #842029;
            border: 1px solid #f5c2c7;
            border-radius: 8px; padding: 10px 14px;
            font-size: 13px; font-weight: 700;
            margin-bottom: 16px;
        }

        @media (max-width: 768px) {
            .auth-container { flex-direction: column; }
            .auth-left { padding: 40px 30px; }
            .auth-left h1 { font-size: 1.6rem; }
        }
    </style>
</head>
<body>

<!-- Header minimal pour pages auth -->
<header>
    <div class="logo">
        <img src="logo.png" width="46px" height="46px" alt="BookNomad">
        <h1>BookNomad</h1>
    </div>
    <nav>
        <a href="index.html">Accueil</a>
        <a href="catalogue.php">Catalogue</a>
        <a href="Inscription.php">Inscription</a>
    </nav>
</header>

<div class="auth-container">
    <div class="auth-left">
        <h1>Bon retour !</h1>
        <p>Accédez instantanément à votre bibliothèque numérique. Retrouvez vos livres, gérez vos emprunts et découvrez de nouvelles lectures.</p>
        <img src="booknomad.avif" alt="Bibliothèque">
    </div>

    <div class="auth-right">
        <div class="auth-form">
            <h2>Connexion</h2>
            <p class="subtitle">Entrez vos identifiants pour accéder à votre compte</p>
            <?php if ($erreur): ?>
                <div class="error-msg"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            <form method="post" action="Connexion.php">
                <input type="email" name="email" placeholder="Adresse email" required
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                <input type="password" name="password" placeholder="Mot de passe" required>
                <button type="submit" name="login">Se connecter →</button>
            </form>
            <p class="link-line">Pas encore de compte ? <a href="Inscription.php">S'inscrire</a></p>
        </div>
    </div>
</div>

<?php require("footer.php"); ?>
</body>
</html>