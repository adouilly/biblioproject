<?php
require_once '../config/database.php';
require_once '../models/Livre.php';
require_once '../models/Ecrivain.php';
require_once '../models/Genre.php';

$livreModel = new Livre();
$ecrivainModel = new Ecrivain();
$genreModel = new Genre();

// Traitement des actions
$message = '';
$type_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'supprimer':
                    $livreModel->supprimer($_POST['id']);
                    $message = "Livre supprimé avec succès !";
                    $type_message = "success";
                    break;
            }
        }
    } catch (Exception $e) {
        $message = "Erreur : " . $e->getMessage();
        $type_message = "danger";
    }
}

// Récupération des données
$livres = $livreModel->listerTous();
$recherche = isset($_GET['recherche']) ? $_GET['recherche'] : '';
if ($recherche) {
    $livres = $livreModel->rechercher($recherche);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Livres - Bibliothèque</title>
    <link rel="stylesheet" href="../assets/css/glassmorphism.css">
</head>
<body>
    <!-- Navigation -->
    <nav class="glass-nav">
        <div class="nav-content">
            <div class="nav-brand">
                <h1>📚 BiblioProject</h1>
            </div>
            <div class="nav-links">
                <a href="../index.php" class="nav-link">🏠 Accueil</a>
                <a href="index.php" class="nav-link active">📖 Livres</a>
                <a href="../ecrivains/index.php" class="nav-link">✍️ Auteurs</a>
                <a href="../genres/index.php" class="nav-link">🎭 Genres</a>
                <a href="../utilisateurs/index.php" class="nav-link">👥 Utilisateurs</a>
                <a href="../emprunts/index.php" class="nav-link">📋 Emprunts</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">📖 Gestion des Livres</h1>
            <p class="page-subtitle">Gérez votre collection de livres</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $type_message ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Actions rapides -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📚</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count($livres) ?></div>
                    <div class="stat-label">Livres total</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count(array_filter($livres, fn($l) => $l['disponible'])) ?></div>
                    <div class="stat-label">Disponibles</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✨</div>
                <div class="stat-content">
                    <a href="ajouter.php" class="btn btn-primary btn-full">
                        ➕ Ajouter un livre
                    </a>
                </div>
            </div>
        </div>

        <!-- Recherche -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <div class="search-input-group">
                    <input type="text" 
                           name="recherche" 
                           placeholder="🔍 Rechercher un livre..."
                           value="<?= htmlspecialchars($recherche) ?>"
                           class="search-input">
                    <button type="submit" class="search-button">Rechercher</button>
                </div>
                <?php if ($recherche): ?>
                    <div class="search-actions">
                        <a href="index.php" class="btn btn-outline btn-sm">🔄 Réinitialiser</a>
                        <span class="search-results">
                            <?= count($livres) ?> résultat(s) pour "<?= htmlspecialchars($recherche) ?>"
                        </span>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Liste des livres -->
        <div class="table-container">
            <?php if (empty($livres)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📖</div>
                    <h3>Aucun livre trouvé</h3>
                    <p>
                        <?php if ($recherche): ?>
                            Aucun livre ne correspond à votre recherche.
                        <?php else: ?>
                            Commencez par ajouter des livres à votre bibliothèque.
                        <?php endif; ?>
                    </p>
                    <a href="ajouter.php" class="btn btn-primary">➕ Ajouter le premier livre</a>
                </div>
            <?php else: ?>
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Titre</th>
                            <th>Auteur</th>
                            <th>Genre</th>
                            <th>Année</th>
                            <th>ISBN</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($livres as $livre): ?>
                            <tr>
                                <td>
                                    <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                        #<?= $livre['id_livre'] ?>
                                    </code>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-dark);">
                                        <?= htmlspecialchars($livre['titre']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div style="color: var(--text-dark);"><?= htmlspecialchars($livre['auteur']) ?></div>
                                    <small style="color: var(--text-light);"><?= htmlspecialchars($livre['nationalite']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($livre['nom_genre']) ?></td>
                                <td><?= $livre['annee_publication'] ?: '-' ?></td>
                                <td><code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px; font-size: 11px;"><?= htmlspecialchars($livre['isbn']) ?: '-' ?></code></td>
                                <td>
                                    <?php if ($livre['disponible']): ?>
                                        <span class="badge badge-success">✅ Disponible</span>
                                    <?php else: ?>
                                        <span class="badge badge-danger">❌ Emprunté</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="modifier.php?id=<?= $livre['id_livre'] ?>" class="btn btn-secondary btn-sm">
                                            ✏️ Modifier
                                        </a>
                                        <form method="POST" style="display: inline;" 
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">
                                            <input type="hidden" name="action" value="supprimer">
                                            <input type="hidden" name="id" value="<?= $livre['id_livre'] ?>">
                                            <button type="submit" class="btn btn-outline btn-sm">
                                                🗑️ Supprimer
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
