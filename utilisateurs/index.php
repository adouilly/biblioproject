<?php
require_once '../config/database.php';
require_once '../models/Utilisateur.php';

$utilisateurModel = new Utilisateur();

$message = '';
$type_message = '';

// Traitement de la suppression
if (isset($_GET['action']) && $_GET['action'] === 'supprimer' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        if ($utilisateurModel->supprimer($id)) {
            $message = "Utilisateur supprimé avec succès";
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

// Récupération de la liste des utilisateurs
$utilisateurs = $recherche ? $utilisateurModel->rechercher($recherche) : $utilisateurModel->listerTous();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs - Bibliothèque</title>
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
                <a href="../genres/index.php" class="nav-link">🎭 Genres</a>
                <a href="index.php" class="nav-link active">👥 Utilisateurs</a>
                <a href="../emprunts/index.php" class="nav-link">📋 Emprunts</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">👥 Gestion des Utilisateurs</h1>
            <p class="page-subtitle">Gérez les membres de votre bibliothèque</p>
        </div>

        <?php if ($message): ?>
            <div class="alert alert-<?= $type_message ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Actions rapides -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">👤</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count($utilisateurs) ?></div>
                    <div class="stat-label">Utilisateurs</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">📧</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count(array_filter($utilisateurs, fn($u) => !empty($u['email']))) ?></div>
                    <div class="stat-label">Avec email</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✨</div>
                <div class="stat-content">
                    <a href="ajouter.php" class="btn btn-primary btn-full">
                        ➕ Ajouter un utilisateur
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
                           placeholder="🔍 Rechercher un utilisateur..."
                           value="<?= htmlspecialchars($recherche) ?>"
                           class="search-input">
                    <button type="submit" class="search-button">Rechercher</button>
                </div>
                <?php if ($recherche): ?>
                    <div class="search-actions">
                        <a href="index.php" class="btn btn-outline btn-sm">🔄 Réinitialiser</a>
                        <span class="search-results">
                            <?= count($utilisateurs) ?> résultat(s) pour "<?= htmlspecialchars($recherche) ?>"
                        </span>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Liste des utilisateurs -->
        <div class="table-container">
            <?php if (empty($utilisateurs)): ?>
                <div class="empty-state">
                    <div class="empty-icon">👥</div>
                    <h3>Aucun utilisateur trouvé</h3>
                    <p>
                        <?php if ($recherche): ?>
                            Aucun utilisateur ne correspond à votre recherche.
                        <?php else: ?>
                            Commencez par ajouter des utilisateurs à votre bibliothèque.
                        <?php endif; ?>
                    </p>
                    <a href="ajouter.php" class="btn btn-primary">➕ Ajouter le premier utilisateur</a>
                </div>
            <?php else: ?>
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom Complet</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                            <tr>
                                <td>
                                    <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                        #<?= $utilisateur['id_utilisateur'] ?>
                                    </code>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-dark);">
                                        <?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($utilisateur['email']): ?>
                                        <a href="mailto:<?= htmlspecialchars($utilisateur['email']) ?>" 
                                           style="color: var(--primary-color); text-decoration: none;">
                                            📧 <?= htmlspecialchars($utilisateur['email']) ?>
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--text-light); font-style: italic;">Non renseigné</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($utilisateur['actif']): ?>
                                        <span class="badge badge-success">✅ Actif</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">⏸️ Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="modifier.php?id=<?= $utilisateur['id_utilisateur'] ?>" class="btn btn-secondary btn-sm">
                                            ✏️ Modifier
                                        </a>
                                        <a href="index.php?action=supprimer&id=<?= $utilisateur['id_utilisateur'] ?>" 
                                           class="btn btn-outline btn-sm"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action supprimera également tous ses emprunts.')">
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
