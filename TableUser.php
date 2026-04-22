<?php
session_start();
require("config.php");

if (!isset($_SESSION['user_id'])) { header("Location: Connexion.php"); exit(); }

$user_id = intval($_SESSION['user_id']);
$nom     = htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur');

$res_actifs = $conn->query("SELECT livres.titre, livres.auteur, livres.categorie, emprunts.date_emprunt, emprunts.status
    FROM emprunts JOIN livres ON emprunts.livre_id = livres.id
    WHERE emprunts.utilisateur_id = $user_id AND emprunts.date_retour IS NULL AND emprunts.status != 'refuse'
    ORDER BY emprunts.date_emprunt DESC");

$total   = $conn->query("SELECT COUNT(*) as c FROM emprunts WHERE utilisateur_id = $user_id")->fetch_assoc()['c'];
$valide  = $conn->query("SELECT COUNT(*) as c FROM emprunts WHERE utilisateur_id = $user_id AND status='valide'")->fetch_assoc()['c'];
$attente = $conn->query("SELECT COUNT(*) as c FROM emprunts WHERE utilisateur_id = $user_id AND status='en_attente'")->fetch_assoc()['c'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mon Compte – BookNomad</title>
<link rel="stylesheet" href="style.css">
<style>
    .grille {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 24px;
        padding: 0 60px 50px;
    }
    .card-titre  { font-family: Cambria, Georgia, serif; font-size: 1.1rem; color: #3e2c1c; }
    .card-auteur { font-size: 0.85rem; color: #7a6a55; font-style: italic; }
    .card-meta   { font-size: 0.82rem; color: #9a7a5a; }
    @media (max-width: 768px) { .grille { padding: 0 16px 30px; } }
</style>
</head>
<body>
<?php require("nav.php"); ?>

<div class="hero">
    <h2>Bienvenue, <?= $nom ?> </h2>
    <p>Voici un aperçu de votre activité sur BookNomad</p>
</div>

<div class="stats">
    <div class="stat-card">
        <div class="stat-number"><?= $total ?></div>
        <div class="stat-label">Total demandes</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $valide ?></div>
        <div class="stat-label">Validées</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $attente ?></div>
        <div class="stat-label">En attente</div>
    </div>
</div>

<div class="section-title"> Mes emprunts en cours</div>

<div class="grille">
<?php
$statusMap = ['en_attente' => 'badge-attente', 'valide' => 'badge-valide', 'refuse' => 'badge-refuse'];
$labelMap  = ['en_attente' => ' En attente', 'valide' => ' Validé', 'refuse' => 'Refusé'];
if ($res_actifs && $res_actifs->num_rows > 0):
    $delay = 0;
    while ($row = $res_actifs->fetch_assoc()):
        $s = $row['status'];
?>
    <div class="card" style="animation: fadeUp 0.5s ease <?= $delay * 0.08 ?>s both">
        <span class="badge-cat"><?= htmlspecialchars(trim($row['categorie'])) ?></span>
        <div class="card-titre"><?= htmlspecialchars($row['titre']) ?></div>
        <div class="card-auteur"> <?= htmlspecialchars($row['auteur']) ?></div>
        <div class="card-meta"> Emprunté le : <?= date('d/m/Y', strtotime($row['date_emprunt'])) ?></div>
        <span class="badge <?= $statusMap[$s] ?? 'badge-attente' ?>"><?= $labelMap[$s] ?? $s ?></span>
    </div>
<?php $delay++; endwhile;
else: ?>
    <div class="vide-msg">Aucun emprunt en cours. <a href="catalogue.php">Parcourir le catalogue </a></div>
<?php endif; ?>
</div>

<?php require("footer.php"); ?>
</body>
</html>