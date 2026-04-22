<?php
session_start();
require("config.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.html"); exit();
}

$feedback = "";

if (isset($_POST['valider'])) {
    $uid = intval($_POST['user_id']); $lid = intval($_POST['livre_id']);
    $conn->query("UPDATE emprunts SET status='valide' WHERE utilisateur_id=$uid AND livre_id=$lid AND status='en_attente'");
    $conn->query("UPDATE livres SET statut='emprunté' WHERE id=$lid");
    $feedback = "valide";
}
if (isset($_POST['refuser'])) {
    $uid = intval($_POST['user_id']); $lid = intval($_POST['livre_id']);
    $conn->query("UPDATE emprunts SET status='refuse' WHERE utilisateur_id=$uid AND livre_id=$lid AND status='en_attente'");
    $feedback = "refuse";
}

$total_attente = $conn->query("SELECT COUNT(*) as c FROM emprunts WHERE status='en_attente'")->fetch_assoc()['c'];
$total_valide  = $conn->query("SELECT COUNT(*) as c FROM emprunts WHERE status='valide'")->fetch_assoc()['c'];
$total_refuse  = $conn->query("SELECT COUNT(*) as c FROM emprunts WHERE status='refuse'")->fetch_assoc()['c'];

$result = $conn->query("SELECT emprunts.utilisateur_id, emprunts.livre_id, emprunts.date_emprunt,
    livres.titre, livres.auteur, livres.categorie,
    utilisateurs.nom, utilisateurs.prenom, utilisateurs.email
    FROM emprunts
    JOIN livres ON emprunts.livre_id = livres.id
    JOIN utilisateurs ON emprunts.utilisateur_id = utilisateurs.id
    WHERE emprunts.status = 'en_attente'
    ORDER BY emprunts.date_emprunt ASC");
if (!$result) die("Erreur SQL: " . $conn->error);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Administration – BookNomad</title>
<link rel="stylesheet" href="style.css">
<style>
    .badge-admin {
        background: #edb55a; color: #3e2c1c; padding: 3px 14px;
        border-radius: 20px; font-size: 12px; font-weight: 700;
        display: inline-block; margin-top: 10px;
    }
    .grille-admin {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 24px;
        padding: 0 60px 60px;
    }
    .card-user {
        background: #f5ebe0; border-radius: 8px;
        padding: 10px 14px; font-size: 0.85rem; color: #5c4033; line-height: 1.7;
    }
    .card-user strong { color: #3e2c1c; }
    .card-date { font-size: 0.8rem; color: #9a7a5a; }
    .card-actions { display: flex; gap: 10px; margin-top: 6px; }
    .btn-valider, .btn-refuser {
        flex: 1; padding: 10px; border: none; border-radius: 8px;
        cursor: pointer; font-family: Cambria, Georgia, serif;
        font-size: 13px; font-weight: 700;
        transition: filter 0.15s, transform 0.15s;
    }
    .btn-valider { background: #2d6a4f; color: white; }
    .btn-valider:hover { filter: brightness(1.15); transform: scale(1.02); }
    .btn-refuser { background: #842029; color: white; }
    .btn-refuser:hover { filter: brightness(1.15); transform: scale(1.02); }
    @media (max-width: 768px) { .grille-admin { padding: 0 16px 40px; } }
</style>
</head>
<body>
<?php require("nav.php"); ?>

<div class="hero">
    <h2>Panel d'Administration </h2>
    <p>Gérez les demandes d'emprunt des utilisateurs</p>
    <span class="badge-admin"> Administrateur : <?= htmlspecialchars($_SESSION['nom']) ?></span>
</div>

<?php if ($feedback === 'valide'): ?>
    <div class="alert alert-success"> Demande validée avec succès.</div>
<?php elseif ($feedback === 'refuse'): ?>
    <div class="alert alert-error"> Demande refusée.</div>
<?php endif; ?>

<div class="stats">
    <div class="stat-card">
        <div class="stat-number"><?= $total_attente ?></div>
        <div class="stat-label">En attente</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $total_valide ?></div>
        <div class="stat-label">Validées</div>
    </div>
    <div class="stat-card">
        <div class="stat-number"><?= $total_refuse ?></div>
        <div class="stat-label">Refusées</div>
    </div>
</div>

<div class="section-title"> Demandes en attente</div>

<div class="grille-admin">
<?php if ($result->num_rows > 0):
    $delay = 0;
    while ($row = $result->fetch_assoc()): ?>
<div class="card" style="animation: fadeUp 0.5s ease <?= $delay * 0.08 ?>s both">
    <span class="badge badge-cat"><?= htmlspecialchars(trim($row['categorie'])) ?></span>
    <div style="font-family:Cambria,Georgia,serif;font-size:1.1rem;color:#3e2c1c;">
        <?= htmlspecialchars($row['titre']) ?>
    </div>
    <div style="font-size:0.85rem;color:#7a6a55;font-style:italic;">
        <?= htmlspecialchars($row['auteur']) ?>
    </div>
    <div class="card-user">
        <strong><?= htmlspecialchars($row['nom'].' '.$row['prenom']) ?></strong><br>
        <?= htmlspecialchars($row['email']) ?>
    </div>
    <div class="card-date">Demandé le : <?= date('d/m/Y à H:i', strtotime($row['date_emprunt'])) ?></div>
    <div class="card-actions">
        <form method="post" style="flex:1">
            <input type="hidden" name="user_id"  value="<?= $row['utilisateur_id'] ?>">
            <input type="hidden" name="livre_id" value="<?= $row['livre_id'] ?>">
            <button type="submit" name="valider" class="btn-valider" style="width:100%"> Valider</button>
        </form>
        <form method="post" style="flex:1" onsubmit="return confirm('Refuser cette demande ?')">
            <input type="hidden" name="user_id"  value="<?= $row['utilisateur_id'] ?>">
            <input type="hidden" name="livre_id" value="<?= $row['livre_id'] ?>">
            <button type="submit" name="refuser" class="btn-refuser" style="width:100%"> Refuser</button>
        </form>
    </div>
</div>
<?php $delay++; endwhile;
else: ?>
    <div class="vide-msg">Aucune demande en attente pour le moment !</div>
<?php endif; ?>
</div>

<?php require("footer.php"); ?>
</body>
</html>