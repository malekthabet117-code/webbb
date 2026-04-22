<?php
session_start();
require("config.php");

$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$result = $conn->query("SELECT * FROM livres WHERE id = $id");
$livre  = $result->fetch_assoc();

if (!$livre) { header("Location: catalogue.php"); exit(); }

$msg = ""; $msgType = "";

if (isset($_POST['emprunter'])) {
    $uid = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

    if ($uid == 0) {
        $msg = "Vous devez être connecté pour emprunter un livre.";
        $msgType = "erreur";
    } elseif ($livre['statut']=='emprunté') {
        $msg = "Ce livre n'est pas disponible actuellement.";
        $msgType = "erreur";
    } else {
        $check = $conn->prepare("SELECT status FROM emprunts 
                                 WHERE utilisateur_id = ? AND livre_id = ? 
                                 AND date_retour IS NULL");
        $check->bind_param("ii", $uid, $id);
        $check->execute();
        $chkRes = $check->get_result();

        if ($chkRes->num_rows > 0) {
            $ex = $chkRes->fetch_assoc();
            if ($ex['status'] === 'en_attente') {
                $msg = "Vous avez déjà une demande en attente pour ce livre.";
                $msgType = "erreur";
            } elseif ($ex['status'] === 'valide') {
                $msg = "Vous empruntez déjà ce livre.";
                $msgType = "erreur";
            }
        } else {
            $stmt = $conn->prepare("INSERT INTO emprunts (id,utilisateur_id, livre_id, date_emprunt, status)
                                    VALUES (?, ?, NOW(), 'en_attente')");
            $stmt->bind_param("ii", $uid, $id);
            if ($stmt->execute()) {
                $msg     = "Demande envoyée ! En attente de validation de l'administrateur.";
                $msgType = "succes";
            } else {
                $msg = "Erreur : " . $conn->error;
                $msgType = "erreur";
            }
        }
    }
}
$autres = $conn->query("SELECT * FROM livres WHERE id != $id LIMIT 3");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($livre['titre']) ?> – BookNomad</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="ficheLivre.css">
</head>
<body>
<?php require("nav.php"); ?>

<div class="breadcrumb">
    <a href="catalogue.php">← Retour au catalogue</a>
    <span>/</span>
    <span><?= htmlspecialchars($livre['titre']) ?></span>
</div>

<main class="fiche-container">

    <div class="fiche-gauche">
        <div class="livre-cover">
            <div class="cover-spine"></div>
            <div class="cover-content">
                <p class="cover-titre"><?= htmlspecialchars($livre['titre']) ?></p>
                <p class="cover-auteur"><?= htmlspecialchars($livre['auteur']) ?></p>
            </div>
        </div>
        <span class="statut-badge <?= $livre['statut'] === 'disponible' ? 'dispo' : 'indispo' ?>">
            <?= $livre['statut'] === 'disponible' ? ' Disponible' : ' Indisponible' ?>
        </span>
    </div>
    <div class="fiche-droite">
        <h1 class="fiche-titre"><?= htmlspecialchars($livre['titre']) ?></h1>
        <p class="fiche-auteur"> <?= htmlspecialchars($livre['auteur']) ?></p>
        <span class="badge-cat"><?= htmlspecialchars(trim($livre['categorie'])) ?></span>
        <hr class="divider">

        <div class="infos-grid">
            <div class="info-item">
                <span class="info-label">Catégorie</span>
                <span class="info-val"><?= htmlspecialchars(trim($livre['categorie'])) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Auteur</span>
                <span class="info-val"><?= htmlspecialchars($livre['auteur']) ?></span>
            </div>
            <div class="info-item">
                <span class="info-label">Statut</span>
                <span class="info-val <?= $livre['statut'] === 'disponible' ? 'txt-dispo' : 'txt-indispo' ?>">
                    <?= ucfirst($livre['statut']) ?>
                </span>
            </div>
            <div class="info-item">
                <span class="info-label">Référence</span>
                <span class="info-val">#<?= $livre['id'] ?></span>
            </div>
        </div>

        <div class="fiche-desc-box">
            <h3>Description</h3>
            <p><?= nl2br(htmlspecialchars($livre['description'])) ?></p>
        </div>

        <?php if ($msg): ?>
            <div class="message <?= $msgType ?>"><?= htmlspecialchars($msg) ?></div>
        <?php endif; ?>

        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="Connexion.php" class="btn-emprunter" style="text-align:center;text-decoration:none;display:block;padding:14px;">
                 Connectez-vous pour emprunter
            </a>
        <?php elseif ($livre['statut'] === 'disponible'): ?>
            <form method="post">
                <button type="submit" name="emprunter" class="btn-emprunter">Emprunter ce livre</button>
            </form>
        <?php else: ?>
            <button class="btn-emprunter desactive" disabled>Livre indisponible</button>
        <?php endif; ?>

        <a href="catalogue.php" class="btn-retour">← Retour au catalogue</a>
    </div>
</main>

<?php if ($autres && $autres->num_rows > 0): ?>
<section class="similaires">
    <h2>Autres livres</h2>
    <div class="sim-grille">
        <?php while ($s = $autres->fetch_assoc()): ?>
        <div class="sim-card">
            <h4><?= htmlspecialchars($s['titre']) ?></h4>
            <p><?= htmlspecialchars($s['auteur']) ?></p>
            <a href="ficheLivre.php?id=<?= $s['id'] ?>">Voir →</a>
        </div>
        <?php endwhile; ?>
    </div>
</section>
<?php endif; ?>

<?php require("footer.php"); ?>
</body>
</html>