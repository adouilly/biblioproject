<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'Bibliothèque - Gestion' ?></title>
    <link rel="stylesheet" href="<?= $cssPath ?? '../assets/css/glassmorphism.css' ?>">
</head>
<body>
    <header>
        <div class="container">
            <h1>📚 Système de Gestion de Bibliothèque</h1>
            <nav>
                <ul>
                    <li><a href="<?= $basePath ?? '../' ?>index.php" <?= ($currentPage ?? '') === 'home' ? 'class="active"' : '' ?>>🏠 Accueil</a></li>
                    <li><a href="<?= $basePath ?? '../' ?>livres/index.php" <?= ($currentPage ?? '') === 'livres' ? 'class="active"' : '' ?>>📖 Livres</a></li>
                    <li><a href="<?= $basePath ?? '../' ?>ecrivains/index.php" <?= ($currentPage ?? '') === 'ecrivains' ? 'class="active"' : '' ?>>✍️ Écrivains</a></li>
                    <li><a href="<?= $basePath ?? '../' ?>genres/index.php" <?= ($currentPage ?? '') === 'genres' ? 'class="active"' : '' ?>>🏷️ Genres</a></li>
                    <li><a href="<?= $basePath ?? '../' ?>utilisateurs/index.php" <?= ($currentPage ?? '') === 'utilisateurs' ? 'class="active"' : '' ?>>👥 Utilisateurs</a></li>
                    <li><a href="<?= $basePath ?? '../' ?>emprunts/index.php" <?= ($currentPage ?? '') === 'emprunts' ? 'class="active"' : '' ?>>📋 Emprunts</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if (isset($content)): ?>
            <?= $content ?>
        <?php else: ?>
            <!-- Contenu de la page sera inclus ici -->
        <?php endif; ?>
    </div>
</body>
</html>
