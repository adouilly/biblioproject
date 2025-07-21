<?php
require_once '../config/database.php';
require_once '../models/Ecrivain.php';

$ecrivainModel = new Ecrivain();

$message = '';
$type_message = '';

// Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        if ($ecrivainModel->supprimer($id)) {
            $message = "Écrivain supprimé avec succès";
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

// Récupération de la liste des écrivains
$ecrivains = $recherche ? $ecrivainModel->rechercher($recherche) : $ecrivainModel->listerTous();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Écrivains - Bibliothèque</title>
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
                <a href="index.php" class="nav-link active">✍️ Auteurs</a>
                <a href="../genres/index.php" class="nav-link">🎭 Genres</a>
                <a href="../utilisateurs/index.php" class="nav-link">👥 Utilisateurs</a>
                <a href="../emprunts/index.php" class="nav-link">📋 Emprunts</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">✍️ Gestion des Auteurs</h1>
            <p class="page-subtitle">Gérez les auteurs de votre bibliothèque</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $type_message ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Actions rapides -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👨‍💼</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count($ecrivains) ?></div>
                    <div class="stat-label">Auteurs total</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">🌍</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count(array_unique(array_column($ecrivains, 'nationalite'))) ?></div>
                    <div class="stat-label">Nationalités</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✨</div>
                <div class="stat-content">
                    <a href="ajouter.php" class="btn btn-primary btn-full">
                        ➕ Ajouter un auteur
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
                           placeholder="🔍 Rechercher un auteur..."
                           value="<?= htmlspecialchars($recherche) ?>"
                           class="search-input">
                    <button type="submit" class="search-button">Rechercher</button>
                </div>
                <?php if ($recherche): ?>
                    <div class="search-actions">
                        <a href="index.php" class="btn btn-outline btn-sm">🔄 Réinitialiser</a>
                        <span class="search-results">
                            <?= count($ecrivains) ?> résultat(s) pour "<?= htmlspecialchars($recherche) ?>"
                        </span>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Liste des auteurs -->
        <div class="table-container">
            <?php if (empty($ecrivains)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📖</div>
                    <h3>Aucun auteur trouvé</h3>
                    <p>
                        <?php if ($recherche): ?>
                            Aucun auteur ne correspond à votre recherche.
                        <?php else: ?>
                            Commencez par ajouter des auteurs à votre bibliothèque.
                        <?php endif; ?>
                    </p>
                    <a href="ajouter.php" class="btn btn-primary">➕ Ajouter le premier auteur</a>
                </div>
            <?php else: ?>
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Nationalité</th>
                            <th>Date de naissance</th>
                            <th>Biographie</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ecrivains as $ecrivain): ?>
                            <tr>
                                <td>
                                    <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                        #<?= $ecrivain['id_ecrivain'] ?>
                                    </code>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-dark);">
                                        <?= htmlspecialchars($ecrivain['nom']) ?>
                                    </div>
                                </td>
                                <td><?= htmlspecialchars($ecrivain['prenom']) ?></td>
                                <td>
                                    <span class="badge badge-info">
                                        🌍 <?= htmlspecialchars($ecrivain['nationalite']) ?>
                                    </span>
                                </td>
                                <td><?= $ecrivain['date_naissance'] ? date('d/m/Y', strtotime($ecrivain['date_naissance'])) : '-' ?></td>
                                <td>
                                    <?php if ($ecrivain['biographie']): ?>
                                        <div style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                            <?= htmlspecialchars(substr($ecrivain['biographie'], 0, 50)) ?><?= strlen($ecrivain['biographie']) > 50 ? '...' : '' ?>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: var(--text-light); font-style: italic;">Aucune biographie</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="modifier.php?id=<?= $ecrivain['id_ecrivain'] ?>" class="btn btn-secondary btn-sm">
                                            ✏️ Modifier
                                        </a>
                                        <a href="index.php?action=supprimer&id=<?= $ecrivain['id_ecrivain'] ?>" 
                                           class="btn btn-outline btn-sm"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet auteur ? Cette action supprimera également tous ses livres.')">
                                            🗑️ Supprimer
                                        </a>
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
