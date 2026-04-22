<?php
session_start();
require("config.php");

if (isset($_SESSION['user_id'])) {
    header("Location: catalogue.php"); exit();
}

$erreur = "";
if (isset($_POST['register'])) {
    $nom      = trim($_POST['nom']);
    $prenom   = trim($_POST['prenom']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($nom) || empty($prenom) || empty($email) || empty($password)) {
        $erreur = "Veuillez remplir tous les champs.";
    } else {
        $check = $conn->prepare("SELECT id FROM utilisateurs WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();
        if ($check->num_rows > 0) {
            $erreur = "Cet email est déjà utilisé.";
        } else {
            $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, email, password, role) VALUES (?, ?, ?, ?, 'user')");
            $stmt->bind_param("ssss", $nom, $prenom, $email, $password);
            if ($stmt->execute()) {
                $_SESSION['user_id'] = $conn->insert_id;
                $_SESSION['nom']     = $nom;
                $_SESSION['prenom']  = $prenom;
                $_SESSION['role']    = 'user';
                header("Location: catalogue.php");
                exit();
            } else {
                $erreur = "Erreur : " . $conn->error;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription – BookNomad</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .auth-container { display: flex; min-height: calc(100vh - 60px); }

        .auth-left {
            flex: 1;
            background: linear-gradient(135deg, #3e2c1c, #5c4033);
            display: flex; flex-direction: column; justify-content: center;
            padding: 80px 60px; color: #f5e6d0;
            position: relative; overflow: hidden;
        }
        .auth-left::before {
            position: absolute; right: -10px; bottom: 20px;
            font-size: 180px; opacity: 0.08;
        }
        .auth-left h1 { font-family: Cambria, Georgia, serif; font-size: 2.2rem; color: #edb55a; margin-bottom: 16px; }
        .auth-left p  { font-size: 1rem; line-height: 1.7; color: #d4b896; max-width: 400px; }
        .auth-left img { margin-top: 30px; border-radius: 12px; max-width: 100%; max-height: 240px; object-fit: cover; opacity: 0.85; }

        .auth-right { flex: 1; background: #fffaf0; display: flex; justify-content: center; align-items: center; padding: 40px; }

        .auth-form { width: 100%; max-width: 400px; }
        .auth-form h2 { font-family: Cambria, Georgia, serif; font-size: 1.7rem; color: #3e2c1c; margin-bottom: 6px; }
        .auth-form .subtitle { font-size: 0.88rem; color: #9a7a5a; margin-bottom: 24px; }

        .auth-form input {
            width: 100%; padding: 12px 16px; margin-bottom: 14px;
            border-radius: 8px; border: 1px solid #d7c2a5; background: #faf4e6;
            font-family: Cambria, Georgia, serif; font-size: 14px; color: #3e2c1c;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .auth-form input:focus { outline: none; border-color: #b08968; box-shadow: 0 0 8px rgba(176,137,104,0.35); }

        .checkbox-group { margin-bottom: 18px; }
        .checkbox-group label { display: flex; align-items: flex-start; gap: 8px; font-size: 13px; color: #5c4033; margin-bottom: 8px; cursor: pointer; }
        .checkbox-group input[type="checkbox"] { width: auto; margin: 0; margin-top: 2px; accent-color: #5c4033; }

        .auth-form button {
            width: 100%; padding: 13px; background: #5c4033; color: #fff8ee;
            border: none; border-radius: 8px; font-family: Cambria, Georgia, serif;
            font-size: 15px; font-weight: 700; cursor: pointer;
            transition: background 0.2s, transform 0.15s;
        }
        .auth-form button:hover { background: #3b2a1a; transform: translateY(-2px); }

        .auth-form .link-line { text-align: center; margin-top: 18px; font-size: 14px; color: #7a6a55; }
        .auth-form .link-line a { color: #b08968; font-weight: 700; text-decoration: none; }
        .auth-form .link-line a:hover { text-decoration: underline; }

        .error-msg { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; border-radius: 8px; padding: 10px 14px; font-size: 13px; font-weight: 700; margin-bottom: 16px; }

        @media (max-width: 768px) {
            .auth-container { flex-direction: column; }
            .auth-left { padding: 40px 30px; }
        }
    </style>
</head>
<body>

<header>
    <div class="logo">
        <img src="logo.png" width="46px" height="46px" alt="BookNomad">
        <h1>BookNomad</h1>
    </div>
    <nav>
        <a href="index.html">Accueil</a>
        <a href="catalogue.php">Catalogue</a>
        <a href="Connexion.php" class="btn-connexion">Connexion</a>
    </nav>
</header>

<div class="auth-container">
    <div class="auth-left">
        <h1>Rejoignez-nous ! </h1>
        <p>Créez votre compte gratuitement et commencez à emprunter des livres. Partagez des ressources éducatives avec toute la communauté étudiante.</p>
        <img src="bon.jpg" alt="Étudiants">
    </div>

    <div class="auth-right">
        <div class="auth-form">
            <h2>Créer un compte</h2>
            <p class="subtitle">Remplissez le formulaire pour vous inscrire</p>
            <?php if ($erreur): ?>
                <div class="error-msg"><?= htmlspecialchars($erreur) ?></div>
            <?php endif; ?>
            <form method="POST" action="Inscription.php">
                <input type="text"     name="nom"      placeholder="Nom *"            required value="<?= htmlspecialchars($_POST['nom']    ?? '') ?>">
                <input type="text"     name="prenom"   placeholder="Prénom *"          required value="<?= htmlspecialchars($_POST['prenom'] ?? '') ?>">
                <input type="email"    name="email"    placeholder="Adresse email *"   required value="<?= htmlspecialchars($_POST['email']  ?? '') ?>">
                <input type="password" name="password" placeholder="Mot de passe *"    required>
                <div class="checkbox-group">
                    <label><input type="checkbox" required> J'accepte le règlement intérieur de la bibliothèque</label>
                    <label><input type="checkbox" required> J'accepte les conditions d'emprunt et de retour</label>
                </div>
                <button type="submit" name="register">S'inscrire →</button>
            </form>
            <p class="link-line">Déjà un compte ? <a href="Connexion.php">Se connecter</a></p>
        </div>
    </div>
</div>

<?php require("footer.php"); ?>
</body>
</html>