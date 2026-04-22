<?php
session_start();
require("config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: Connexion.php");
    exit();
}

$message = "";
$messageType = "";

if (isset($_POST['ajouter'])) {
    $titre       = trim($_POST["titre"]);
    $auteur      = trim($_POST["auteur"]);
    $description = trim($_POST["description"]);
    $cat         = $_POST["categorie"];

    if (empty($titre) || empty($auteur) || $cat === "0") {
        $message = "Veuillez remplir tous les champs obligatoires et choisir une catégorie.";
        $messageType = "error";
    } else {
        $stmt = $conn->prepare("INSERT INTO livres (titre, auteur, description, categorie, statut) VALUES (?, ?, ?, ?, 'disponible')");
        $stmt->bind_param("ssss", $titre, $auteur, $description, $cat);
        if ($stmt->execute()) {
            header("Location: catalogue.php?success=1");
            exit();
        } else {
            $message = "Erreur lors de l'ajout : " . $conn->error;
            $messageType = "error";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Livre – BookNomad</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .form-container {
            max-width: 520px;
            margin: 50px auto 60px;
            background: #fffaf0;
            border: 2px solid #c8a97e;
            border-top: 6px solid #5c4033;
            border-radius: 14px;
            padding: 36px 40px;
            box-shadow: 5px 10px 30px rgba(92,64,51,0.16);
        }

        .form-container h2 {
            text-align: center;
            font-size: 1.6rem;
            color: #3e2c1c;
            margin-bottom: 28px;
            font-family: Cambria, Georgia, serif;
        }

        .form-container input,
        .form-container textarea,
        .form-container select {
            width: 100%;
            padding: 11px 14px;
            margin-bottom: 16px;
            border-radius: 8px;
            border: 1px solid #d7c2a5;
            background-color: #faf4e6;
            font-family: Cambria, Georgia, serif;
            font-size: 14px;
            color: #3e2c1c;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-container input:focus,
        .form-container textarea:focus,
        .form-container select:focus {
            outline: none;
            border-color: #b08968;
            box-shadow: 0 0 8px rgba(176,137,104,0.35);
        }

        .form-container textarea { resize: vertical; min-height: 100px; }

        .form-container .btn-submit {
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

        .form-container .btn-submit:hover {
            background: #3b2a1a;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>
<?php require("nav.php"); ?>

<div class="hero" ">
    <h2>Ajouter un Livre</h2>
    <p>Partagez un livre avec la communauté BookNomad</p>
</div>

<?php if (!empty($message)): ?>
    <div class="alert alert-error" style="margin: 20px auto;"><?= htmlspecialchars($message) ?></div>
<?php endif; ?>

<div class="form-container">
    <h2>Nouveau Livre</h2>
    <form method="post" action="ajouterLivre.php">
        <input type="text" name="titre" placeholder="Titre du livre *"
               value="<?= htmlspecialchars($_POST['titre'] ?? '') ?>" required>
        <input type="text" name="auteur" placeholder="Auteur *"
               value="<?= htmlspecialchars($_POST['auteur'] ?? '') ?>" required>
        <textarea name="description" placeholder="Description (optionnelle)"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
        <select name="categorie">
            <option value="0">-- Choisir une catégorie *--</option>
            <option value="Science"      <?= (($_POST['categorie'] ?? '') === 'Science')      ? 'selected' : '' ?>>Science</option>
            <option value="Informatique" <?= (($_POST['categorie'] ?? '') === 'Informatique') ? 'selected' : '' ?>>Informatique</option>
            <option value="Philosophy"   <?= (($_POST['categorie'] ?? '') === 'Philosophy')   ? 'selected' : '' ?>>Philosophie</option>
            <option value="Francais"     <?= (($_POST['categorie'] ?? '') === 'Francais')     ? 'selected' : '' ?>>Langue / Français</option>
        </select>
        <button type="submit" name="ajouter" class="btn-submit">Ajouter au catalogue</button>
    </form>
</div>

<?php require("footer.php"); ?>
</body>
</html>