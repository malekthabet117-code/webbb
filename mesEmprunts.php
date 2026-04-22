<?php
session_start();
require("config.php");

if (!isset($_SESSION['user_id'])) { header("Location: Connexion.php"); exit(); }

$user_id       = intval($_SESSION['user_id']);
$successRetour = false;

if (isset($_POST['retour'])) {
    $livre_id = intval($_POST['livre_id']);
    $conn->query("UPDATE emprunts SET date_retour = NOW() WHERE livre_id = $livre_id AND utilisateur_id = $user_id AND date_retour IS NULL");
    $conn->query("UPDATE livres SET statut = 'disponible' WHERE id = $livre_id");
    $successRetour = true;
}

$result = $conn->query("SELECT livres.id, livres.titre, livres.auteur, livres.categorie,
        emprunts.date_emprunt, emprunts.status
        FROM emprunts JOIN livres ON emprunts.livre_id = livres.id
        WHERE emprunts.utilisateur_id = $user_id
        AND emprunts.date_retour IS NULL
        AND emprunts.status != 'refuse'
        ORDER BY emprunts.date_emprunt DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mes Emprunts – BookNomad</title>
<link rel="stylesheet" href="style.css">
<style>
    .btn-retour-action {
        background: #5c4033; color: #fff8ee; border: none; border-radius: 8px;
        padding: 8px 18px; cursor: pointer; font-family: Cambria, Georgia, serif;
        font-size: 13px; font-weight: 700; transition: background 0.2s, transform 0.15s;
    }
    .btn-retour-action:hover { background: #3b2a1a; transform: scale(1.03); }
    .en-attente-info { font-size: 12px; color: #9a7a5a; font-style: italic; }
</style>
</head>
<body>
<?php require("nav.php"); ?>

<div class="hero">
    <h2>Mes Emprunts</h2>
    <p>Livres actuellement en votre possession</p>
</div>

<?php if ($successRetour): ?>
<div class="alert alert-success"> Livre retourné avec succès ! Il est de nouveau disponible dans le catalogue.</div>
<?php endif; ?>

<div class="section-title">Emprunts en cours</div>

<div class="table-wrap">
<?php if ($result && $result->num_rows > 0): ?>
<table>
    <thead>
        <tr>
            <th>Livre</th>
            <th>Auteur</th>
            <th>Catégorie</th>
            <th>Date d'emprunt</th>
            <th>Statut</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $statusMap = ['en_attente' => 'badge-attente', 'valide' => 'badge-valide', 'refuse' => 'badge-refuse'];
    $labelMap  = ['en_attente' => ' En attente', 'valide' => ' Validé', 'refuse' => 'Refusé'];
    while ($row = $result->fetch_assoc()):
        $s = $row['status'];
    ?>
    <tr>
        <td><strong><?= htmlspecialchars($row['titre']) ?></strong></td>
        <td><?= htmlspecialchars($row['auteur']) ?></td>
        <td><span class="badge badge-cat"><?= htmlspecialchars(trim($row['categorie'])) ?></span></td>
        <td><?= date('d/m/Y à H:i', strtotime($row['date_emprunt'])) ?></td>
        <td><span class="badge <?= $statusMap[$s] ?? 'badge-attente' ?>"><?= $labelMap[$s] ?? $s ?></span></td>
        <td>
            <?php if ($s === 'valide'): ?>
            <form method="post" onsubmit="return confirm('Confirmer le retour de ce livre ?')">
                <input type="hidden" name="livre_id" value="<?= $row['id'] ?>">
                <button type="submit" name="retour" class="btn-retour-action"> Retourner</button>
            </form>
            <?php else: ?>
                <span class="en-attente-info">En attente de validation</span>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<div class="vide-msg">Vous n'avez aucun emprunt en cours. <a href="catalogue.php">Parcourir le catalogue </a></div>
<?php endif; ?>
</div>

<?php require("footer.php"); ?>
</body>
</html>