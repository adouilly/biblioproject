<?php
require_once '../config/database.php';
require_once '../models/Genre.php';

$genreModel = new Genre();

$message = '';
$type_message = '';

// Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        if ($genreModel->supprimer($id)) {
            $message = "Genre supprimé avec succès";
            $type_message = "success";
        } else {
            $message = "Erreur lors de la suppression";
            $type_message = "danger";
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $type_message = "danger";
    }
}

// Récupération des messages de redirection
if (isset($_GET['message'])) {
    $message = $_GET['message'];
    $type_message = $_GET['type'] ?? 'info';
}

// Récupération du terme de recherche
$recherche = $_GET['recherche'] ?? '';

// Récupération de la liste des genres
$genres = $recherche ? $genreModel->rechercher($recherche) : $genreModel->listerTous();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Genres - Bibliothèque</title>
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
                <a href="../livres/index.php" class="nav-link">📖 Livres</a>
                <a href="../ecrivains/index.php" class="nav-link">✍️ Auteurs</a>
                <a href="index.php" class="nav-link active">🎭 Genres</a>
                <a href="../utilisateurs/index.php" class="nav-link">👥 Utilisateurs</a>
                <a href="../emprunts/index.php" class="nav-link">📋 Emprunts</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">🎭 Gestion des Genres</h1>
            <p class="page-subtitle">Organisez les catégories de votre bibliothèque</p>
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
                    <div class="stat-number"><?= count($genres) ?></div>
                    <div class="stat-label">Genres total</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🎨</div>
                <div class="stat-content">
                    <div class="stat-number"><?= array_sum(array_column($genres, 'nb_livres')) ?></div>
                    <div class="stat-label">Livres classés</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✨</div>
                <div class="stat-content">
                    <a href="ajouter.php" class="btn btn-primary btn-full">
                        ➕ Ajouter un genre
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
                           placeholder="🔍 Rechercher un genre..."
                           value="<?= htmlspecialchars($recherche) ?>"
                           class="search-input">
                    <button type="submit" class="search-button">Rechercher</button>
                </div>
                <?php if ($recherche): ?>
                    <div class="search-actions">
                        <a href="index.php" class="btn btn-outline btn-sm">🔄 Réinitialiser</a>
                        <span class="search-results">
                            <?= count($genres) ?> résultat(s) pour "<?= htmlspecialchars($recherche) ?>"
                        </span>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Liste des genres -->
        <div class="table-container">
            <?php if (empty($genres)): ?>
                <div class="empty-state">
                    <div class="empty-icon">🎭</div>
                    <h3>Aucun genre trouvé</h3>
                    <p>
                        <?php if ($recherche): ?>
                            Aucun genre ne correspond à votre recherche.
                        <?php else: ?>
                            Commencez par ajouter des genres à votre bibliothèque.
                        <?php endif; ?>
                    </p>
                    <a href="ajouter.php" class="btn btn-primary">➕ Ajouter le premier genre</a>
                </div>
            <?php else: ?>
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom du Genre</th>
                            <th>Description</th>
                            <th>Nombre de Livres</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($genres as $genre): ?>
                            <tr>
                                <td>
                                    <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                        #<?= $genre['id_genre'] ?>
                                    </code>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-dark);">
                                        🎭 <?= htmlspecialchars($genre['nom_genre']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($genre['description']): ?>
                                        <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= htmlspecialchars(substr($genre['description'], 0, 80)) ?><?= strlen($genre['description']) > 80 ? '...' : '' ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-light); font-style: italic;">Aucune description</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($genre['nb_livres'] > 0): ?>
                                        <span class="badge badge-success">
                                            📖 <?= $genre['nb_livres'] ?> livre(s)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">
                                            📭 Aucun livre
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="modifier.php?id=<?= $genre['id_genre'] ?>" class="btn btn-secondary btn-sm">
                                            ✏️ Modifier
                                        </a>
                                        <?php if ($genre['nb_livres'] == 0): ?>
                                            <a href="index.php?action=supprimer&id=<?= $genre['id_genre'] ?>" 
                                               class="btn btn-outline btn-sm"
                                               onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce genre ?')">
                                                🗑️ Supprimer
                                            </a>
                                        <?php else: ?>
                                            <button class="btn btn-outline btn-sm" 
                                                    disabled 
                                                    title="Impossible de supprimer un genre contenant des livres"
                                                    style="opacity: 0.5; cursor: not-allowed;">
                                                🔒 Protégé
                                            </button>
                                        <?php endif; ?>
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
