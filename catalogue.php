<?php
session_start();
require("config.php");
$successMsg = isset($_GET['success']) && $_GET['success'] == '1';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Catalogue – BookNomad</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .page-title { text-align: center; padding: 40px 20px 10px; }
        .page-title h2 { font-size: 2rem; color: #4b3320; margin-bottom: 8px; }
        .page-title p  { font-size: 1rem; color: #7a5c3a; }

        .grille {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 28px;
            padding: 10px 50px 60px;
        }
        .card-top {
            display: flex; justify-content: space-between; align-items: center;
            padding-bottom: 10px; border-bottom: 1px dashed #d6c2a1; flex-wrap: wrap; gap: 6px;
        }
        .badge-statut { font-size: 11px; font-weight: 700; padding: 4px 10px; border-radius: 20px; }
        .statut-dispo   { background: #d4edda; color: #2d6a4f; border: 1px solid #a8d5ba; }
        .statut-indispo { background: #f8d7da; color: #842029; border: 1px solid #f5c2c7; }
        .card-titre  { font-size: 1.1rem; font-weight: bold; color: #3e2f1c; line-height: 1.3; }
        .card-auteur { font-size: 0.88rem; color: #7a6a55; font-style: italic; }
        .card-desc   { font-size: 0.88rem; color: #5a4a36; line-height: 1.6; flex-grow: 1; }
        .btn-voir {
            display: block; text-align: center; padding: 9px 16px;
            background: #5c4033; color: #fff8ee; border-radius: 8px;
            text-decoration: none; font-size: 13px; font-weight: 700;
            font-family: Cambria, Georgia, serif; transition: background 0.2s; margin-top: 4px;
        }
        .btn-voir:hover { background: #3b2a1a; }
        @media (max-width: 1100px) { .grille { grid-template-columns: repeat(2,1fr); padding: 10px 30px 40px; } }
        @media (max-width: 650px)  { .grille { grid-template-columns: 1fr; padding: 10px 16px 30px; } }
    </style>
</head>
<body>
<?php require("nav.php"); ?>

<?php if ($successMsg): ?>
<div class="alert alert-success">Le livre a été ajouté avec succès et apparaît maintenant dans le catalogue !</div>
<?php endif; ?>

<section class="page-title">
    <h2>Catalogue des Livres</h2>
    <p>Parcourez les livres disponibles par catégorie</p>
</section>

<div class="filtres">
    <button class="btn-filtre active" onclick="filtrer('tous',this)">Tous</button>
    <button class="btn-filtre" onclick="filtrer('Informatique',this)">Informatique</button>
    <button class="btn-filtre" onclick="filtrer('Science',this)">Science</button>
    <button class="btn-filtre" onclick="filtrer('Philosophy',this)">Philosophie</button>
    <button class="btn-filtre" onclick="filtrer('Francais',this)">Langues</button>
</div>

<div class="grille" id="grille">
<?php
$result = $conn->query("SELECT * FROM livres ORDER BY categorie, titre");
if ($result->num_rows === 0) {
    echo "<p class='vide-msg'>Aucun livre disponible pour le moment.</p>";
} else {
    while ($row = $result->fetch_assoc()) {
        $titre     = htmlspecialchars($row['titre']);
        $auteur    = htmlspecialchars($row['auteur']);
        $desc      = htmlspecialchars($row['description']);
        $categorie = htmlspecialchars(trim($row['categorie']));
        $statut    = htmlspecialchars($row['statut']);
        $id        = (int)$row['id'];
        $sClass    = ($statut === 'disponible') ? 'statut-dispo'  : 'statut-indispo';
        $sLabel    = ($statut === 'disponible') ? 'Disponible'    : 'Indisponible';
        echo "
        <div class='card' data-cat='{$categorie}'>
            <div class='card-top'>
                <span class='badge-cat'>{$categorie}</span>
                <span class='badge-statut {$sClass}'>{$sLabel}</span>
            </div>
            <h3 class='card-titre'>{$titre}</h3>
            <p class='card-auteur'>{$auteur}</p>
            <p class='card-desc'>{$desc}</p>
            <a href='ficheLivre.php?id={$id}' class='btn-voir'>Voir le livre →</a>
        </div>";
    }
}
?>
</div>

<?php require("footer.php"); ?>
<script>
function filtrer(cat, btn) {
    document.querySelectorAll('.btn-filtre').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    document.querySelectorAll('.card').forEach(card => {
        card.style.display = (cat === 'tous' || card.dataset.cat === cat) ? 'flex' : 'none';
    });
}
</script>
</body>
</html>