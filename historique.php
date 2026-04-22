<?php
session_start();
require("config.php");

if (!isset($_SESSION['user_id'])) { header("Location: Connexion.php"); exit(); }

$user_id = intval($_SESSION['user_id']);
$nom     = htmlspecialchars($_SESSION['nom'] ?? 'Utilisateur');

$result = $conn->query("SELECT livres.titre, livres.auteur, livres.categorie,
    emprunts.date_emprunt, emprunts.date_retour, emprunts.status
    FROM emprunts JOIN livres ON emprunts.livre_id = livres.id
    WHERE emprunts.utilisateur_id = $user_id
    ORDER BY emprunts.date_emprunt DESC");
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Historique – BookNomad</title>
<link rel="stylesheet" href="style.css">
<style>
    .en-cours { color: #e8a838; font-weight: 700; font-style: italic; font-size: 12px; }
</style>
</head>
<body>
<?php require("nav.php"); ?>

<div class="hero">
    <h2>Historique des Emprunts </h2>
    <p>Tous vos emprunts passés et en cours, <?= $nom ?></p>
</div>

<div class="section-title"> Tous mes emprunts</div>

<div class="filtres">
    <button class="btn-filtre active" onclick="filtrerStatus('tous',this)">Tous</button>
    <button class="btn-filtre" onclick="filtrerStatus('valide',this)">Validés</button>
    <button class="btn-filtre" onclick="filtrerStatus('en_attente',this)">En attente</button>
    <button class="btn-filtre" onclick="filtrerStatus('refuse',this)">Refusés</button>
</div>

<div class="table-wrap">
<?php if ($result && $result->num_rows > 0): ?>
<table id="tableHistorique">
    <thead>
        <tr>
            <th>Livre</th>
            <th>Auteur</th>
            <th>Catégorie</th>
            <th>Date d'emprunt</th>
            <th>Date de retour</th>
            <th>Statut</th>
        </tr>
    </thead>
    <tbody>
    <?php
    $statusMap = ['en_attente' => 'badge-attente', 'valide' => 'badge-valide', 'refuse' => 'badge-refuse'];
    $labelMap  = ['en_attente' => ' En attente', 'valide' => ' Validé', 'refuse' => 'Refusé'];
    while ($row = $result->fetch_assoc()):
        $s = $row['status'];
    ?>
    <tr data-status="<?= htmlspecialchars($s) ?>">
        <td><strong><?= htmlspecialchars($row['titre']) ?></strong></td>
        <td><?= htmlspecialchars($row['auteur']) ?></td>
        <td><span class="badge badge-cat"><?= htmlspecialchars(trim($row['categorie'])) ?></span></td>
        <td><?= date('d/m/Y à H:i', strtotime($row['date_emprunt'])) ?></td>
        <td>
            <?php if ($row['date_retour']): ?>
                <?= date('d/m/Y à H:i', strtotime($row['date_retour'])) ?>
            <?php else: ?>
                <span class="en-cours"> En cours</span>
            <?php endif; ?>
        </td>
        <td><span class="badge <?= $statusMap[$s] ?? 'badge-attente' ?>"><?= $labelMap[$s] ?? $s ?></span></td>
    </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<?php else: ?>
<div class="vide-msg">Aucun historique trouvé. <a href="catalogue.php">Commencez par emprunter un livre </a></div>
<?php endif; ?>
</div>

<?php require("footer.php"); ?>
<script>
function filtrerStatus(status, btn) {
    document.querySelectorAll('.btn-filtre').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('#tableHistorique tbody tr').forEach(row => {
        row.style.display = (status === 'tous' || row.dataset.status === status) ? '' : 'none';
    });
}
</script>
</body>
</html>