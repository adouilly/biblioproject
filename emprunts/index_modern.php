<?php
require_once '../config/database.php';
require_once '../models/Emprunt.php';

$empruntModel = new Emprunt();

$message = '';
$type_message = '';

// Traitement du retour de livre
if (isset($_POST['action']) && $_POST['action'] === 'retourner' && isset($_POST['id'])) {
    $id = (int)$_POST['id'];
    try {
        if ($empruntModel->retournerLivre($id)) {
            $message = "Livre retourné avec succès";
            $type_message = "success";
        } else {
            $message = "Erreur lors du retour";
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

// Récupération de la liste des emprunts
$emprunts = $recherche ? $empruntModel->rechercher($recherche) : $empruntModel->listerTous();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Emprunts - Bibliothèque</title>
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
                <a href="../utilisateurs/index.php" class="nav-link">👥 Utilisateurs</a>
                <a href="index.php" class="nav-link active">📋 Emprunts</a>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="page-header">
            <h1 class="page-title">📋 Gestion des Emprunts</h1>
            <p class="page-subtitle">Suivez les prêts de votre bibliothèque</p>
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
                    <div class="stat-number"><?= count($emprunts) ?></div>
                    <div class="stat-label">Emprunts total</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⏳</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count(array_filter($emprunts, fn($e) => is_null($e['date_retour']))) ?></div>
                    <div class="stat-label">En cours</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✨</div>
                <div class="stat-content">
                    <a href="ajouter.php" class="btn btn-primary btn-full">
                        ➕ Nouvel emprunt
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
                           placeholder="🔍 Rechercher un emprunt..."
                           value="<?= htmlspecialchars($recherche) ?>"
                           class="search-input">
                    <button type="submit" class="search-button">Rechercher</button>
                </div>
                <?php if ($recherche): ?>
                    <div class="search-actions">
                        <a href="index.php" class="btn btn-outline btn-sm">🔄 Réinitialiser</a>
                        <span class="search-results">
                            <?= count($emprunts) ?> résultat(s) pour "<?= htmlspecialchars($recherche) ?>"
                        </span>
                    </div>
                <?php endif; ?>
            </form>
        </div>

        <!-- Liste des emprunts -->
        <div class="table-container">
            <?php if (empty($emprunts)): ?>
                <div class="empty-state">
                    <div class="empty-icon">📋</div>
                    <h3>Aucun emprunt trouvé</h3>
                    <p>
                        <?php if ($recherche): ?>
                            Aucun emprunt ne correspond à votre recherche.
                        <?php else: ?>
                            Commencez par enregistrer des emprunts.
                        <?php endif; ?>
                    </p>
                    <a href="ajouter.php" class="btn btn-primary">➕ Enregistrer le premier emprunt</a>
                </div>
            <?php else: ?>
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Livre</th>
                            <th>Utilisateur</th>
                            <th>Date d'emprunt</th>
                            <th>Date de retour prévue</th>
                            <th>Date de retour réelle</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($emprunts as $emprunt): ?>
                            <tr>
                                <td>
                                    <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                        #<?= $emprunt['id_emprunt'] ?>
                                    </code>
                                </td>
                                <td>
                                    <div style="color: var(--text-dark);">
                                        <strong>📖 <?= htmlspecialchars($emprunt['titre']) ?></strong>
                                    </div>
                                    <small style="color: var(--text-light);">par <?= htmlspecialchars($emprunt['auteur']) ?></small>
                                </td>
                                <td>
                                    <div style="color: var(--text-dark);">
                                        👤 <?= htmlspecialchars($emprunt['utilisateur']) ?>
                                    </div>
                                </td>
                                <td><?= date('d/m/Y', strtotime($emprunt['date_emprunt'])) ?></td>
                                <td>
                                    <?php 
                                    $date_retour_prevue = strtotime($emprunt['date_retour_prevue']);
                                    $aujourd_hui = time();
                                    $est_en_retard = $date_retour_prevue < $aujourd_hui && is_null($emprunt['date_retour']);
                                    ?>
                                    <span style="color: <?= $est_en_retard ? '#e74c3c' : 'var(--text-dark)' ?>">
                                        <?= date('d/m/Y', $date_retour_prevue) ?>
                                        <?php if ($est_en_retard): ?>
                                            ⚠️
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $emprunt['date_retour'] ? date('d/m/Y', strtotime($emprunt['date_retour'])) : '-' ?>
                                </td>
                                <td>
                                    <?php if ($emprunt['date_retour']): ?>
                                        <span class="badge badge-success">✅ Retourné</span>
                                    <?php elseif ($est_en_retard): ?>
                                        <span class="badge badge-danger">⚠️ En retard</span>
                                    <?php else: ?>
                                        <span class="badge badge-warning">⏳ En cours</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if (!$emprunt['date_retour']): ?>
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="action" value="retourner">
                                                <input type="hidden" name="id" value="<?= $emprunt['id_emprunt'] ?>">
                                                <button type="submit" class="btn btn-secondary btn-sm">
                                                    ↩️ Retourner
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        <a href="modifier.php?id=<?= $emprunt['id_emprunt'] ?>" class="btn btn-outline btn-sm">
                                            ✏️ Modifier
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
