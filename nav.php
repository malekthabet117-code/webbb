<?php

$current = basename($_SERVER['PHP_SELF']);

function navLink($href, $label, $current, $extraClass = '') {
    $active = (basename($href) === $current) ? ' active' : '';
    $class = trim("$extraClass$active");
    $classAttr = $class ? " class=\"$class\"" : '';
    echo "<a href=\"$href\"$classAttr>$label</a>";
}
?>
<header>
    <div class="logo">
        <img src="logo.png" width="46px" height="46px" alt="BookNomad">
        <h1>BookNomad</h1>
    </div>
    <nav>
        <?php navLink('index.html', 'Accueil', $current); ?>
        <?php navLink('catalogue.php', 'Catalogue', $current); ?>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                <?php navLink('admin.php', ' Admin', $current); ?>
            <?php else: ?>
                <?php navLink('ajouterLivre.php', 'Ajouter Livre', $current); ?>
                <?php navLink('mesEmprunts.php', 'Mes Emprunts', $current); ?>
                <?php navLink('historique.php', 'Historique', $current); ?>
                <?php navLink('TableUser.php', 'Mon Compte', $current); ?>
            <?php endif; ?>
            <a href="deconnexion.php" class="btn-deconnexion">Déconnexion</a>
        <?php else: ?>
            <a href="Connexion.php" class="btn-connexion">Connexion</a>
            <?php navLink('Inscription.php', 'Inscription', $current); ?>
        <?php endif; ?>
    </nav>
</header>