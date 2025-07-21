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

// Traitement du nettoyage des anciens retours
if (isset($_POST['action']) && $_POST['action'] === 'nettoyer') {
    try {
        if ($empruntModel->nettoyerAnciensRetours()) {
            $message = "Anciens retours supprimés avec succès";
            $type_message = "success";
        }
    } catch (Exception $e) {
        $message = "Erreur lors du nettoyage : " . $e->getMessage();
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

// Récupération des onglets
$onglet_actif = $_GET['onglet'] ?? 'en_cours';

// Récupération des données selon l'onglet
switch ($onglet_actif) {
    case 'retours':
        $emprunts = $recherche ? $empruntModel->rechercher($recherche) : $empruntModel->listerRetoursRecents();
        break;
    case 'en_cours':
    default:
        $emprunts = $recherche ? $empruntModel->rechercher($recherche) : $empruntModel->listerEnCours();
        break;
}

// Statistiques pour les badges
$stats = $empruntModel->obtenirStatistiques();
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
                    <div class="stat-number"><?= $stats['en_cours'] ?></div>
                    <div class="stat-label">En cours</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">✅</div>
                <div class="stat-content">
                    <div class="stat-number"><?= count($empruntModel->listerRetoursRecents()) ?></div>
                    <div class="stat-label">Retours récents</div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-icon">⚠️</div>
                <div class="stat-content">
                    <div class="stat-number"><?= $stats['en_retard'] ?></div>
                    <div class="stat-label">En retard</div>
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

        <!-- Système d'onglets -->
        <div class="tabs-container">
            <!-- En-tête des onglets -->
            <div class="tabs-header">
                <button class="tab-button <?= $onglet_actif === 'en_cours' ? 'active' : '' ?>" 
                        onclick="switchTab('en_cours')">
                    📚 Emprunts en cours
                    <span class="tab-badge"><?= $stats['en_cours'] ?></span>
                </button>
                <button class="tab-button <?= $onglet_actif === 'retours' ? 'active' : '' ?>" 
                        onclick="switchTab('retours')">
                    ✅ Retours récents
                    <span class="tab-badge success"><?= count($empruntModel->listerRetoursRecents()) ?></span>
                </button>
            </div>

            <!-- Contenu des onglets -->
            <div class="tab-content">
                <!-- Recherche -->
                <div class="search-container">
                    <form method="GET" class="search-form">
                        <input type="hidden" name="onglet" value="<?= $onglet_actif ?>">
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
                                <a href="index.php?onglet=<?= $onglet_actif ?>" class="btn btn-outline btn-sm">🔄 Réinitialiser</a>
                                <span class="search-results">
                                    <?= count($emprunts) ?> résultat(s) pour "<?= htmlspecialchars($recherche) ?>"
                                </span>
                            </div>
                        <?php endif; ?>
                    </form>
                </div>

                <!-- Panneau Emprunts en cours -->
                <div id="tab-en-cours" class="tab-panel <?= $onglet_actif === 'en_cours' ? 'active' : '' ?>">
                    <div class="tab-actions">
                        <div class="tab-info">
                            📊 <?= count($emprunts) ?> emprunt(s) en cours
                        </div>
                    </div>
                    
                    <!-- Tableau emprunts en cours -->
                    <?php if (empty($emprunts)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">📋</div>
                            <h3>Aucun emprunt en cours</h3>
                            <p>Tous les livres sont disponibles pour l'emprunt.</p>
                            <a href="ajouter.php" class="btn btn-primary">➕ Nouvel emprunt</a>
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
                                                👤 <?= htmlspecialchars($emprunt['emprunteur']) ?>
                                            </div>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($emprunt['date_emprunt'])) ?></td>
                                        <td>
                                            <?php 
                                            $date_retour_prevue = strtotime($emprunt['date_retour_prevue']);
                                            $est_en_retard = $emprunt['statut_detail'] === 'EN RETARD';
                                            ?>
                                            <span style="color: <?= $est_en_retard ? '#e74c3c' : 'var(--text-dark)' ?>">
                                                <?= date('d/m/Y', $date_retour_prevue) ?>
                                                <?php if ($est_en_retard): ?>
                                                    ⚠️
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($emprunt['statut_detail'] === 'EN RETARD'): ?>
                                                <span class="badge badge-danger">⚠️ En retard</span>
                                            <?php elseif ($emprunt['statut_detail'] === 'BIENTÔT DÛ'): ?>
                                                <span class="badge badge-warning">⏰ Bientôt dû</span>
                                            <?php else: ?>
                                                <span class="badge badge-info">⏳ En cours</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="action" value="retourner">
                                                    <input type="hidden" name="id" value="<?= $emprunt['id_emprunt'] ?>">
                                                    <button type="submit" class="btn btn-success btn-sm" onclick="return confirm('Confirmer le retour de ce livre ?')">
                                                        ↩️ Retourner
                                                    </button>
                                                </form>
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

                <!-- Panneau Retours récents -->
                <div id="tab-retours" class="tab-panel <?= $onglet_actif === 'retours' ? 'active' : '' ?>">
                    <div class="tab-actions">
                        <div class="tab-info">
                            📊 <?= count($empruntModel->listerRetoursRecents()) ?> retour(s) récent(s) (< 3 mois)
                        </div>
                        <form method="POST" style="display: inline;">
                            <input type="hidden" name="action" value="nettoyer">
                            <button type="submit" class="cleanup-button" onclick="return confirm('Supprimer tous les retours de plus de 3 mois ?')">
                                🧹 Nettoyer archives
                            </button>
                        </form>
                    </div>
                    
                    <!-- Tableau retours récents -->
                    <?php 
                    $retours_recents = $onglet_actif === 'retours' ? $emprunts : $empruntModel->listerRetoursRecents();
                    if (empty($retours_recents)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">✅</div>
                            <h3>Aucun retour récent</h3>
                            <p>Aucun livre n'a été retourné dans les 3 derniers mois.</p>
                        </div>
                    <?php else: ?>
                        <table class="glass-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Livre</th>
                                    <th>Utilisateur</th>
                                    <th>Date d'emprunt</th>
                                    <th>Date de retour</th>
                                    <th>Durée</th>
                                    <th>Statut</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($retours_recents as $retour): ?>
                                    <tr>
                                        <td>
                                            <code style="background: rgba(255,255,255,0.3); padding: 2px 6px; border-radius: 4px; font-size: 11px;">
                                                #<?= $retour['id_emprunt'] ?>
                                            </code>
                                        </td>
                                        <td>
                                            <div style="color: var(--text-dark);">
                                                <strong>📖 <?= htmlspecialchars($retour['titre']) ?></strong>
                                            </div>
                                            <small style="color: var(--text-light);">par <?= htmlspecialchars($retour['auteur']) ?></small>
                                        </td>
                                        <td>
                                            <div style="color: var(--text-dark);">
                                                👤 <?= htmlspecialchars($retour['emprunteur']) ?>
                                            </div>
                                        </td>
                                        <td><?= date('d/m/Y', strtotime($retour['date_emprunt'])) ?></td>
                                        <td><?= date('d/m/Y', strtotime($retour['date_retour_effective'])) ?></td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?= $retour['duree_emprunt'] ?> jours
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge badge-success">✅ Retourné</span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript pour les onglets -->
    <script>
        function switchTab(tabName) {
            // Redirection avec paramètre onglet
            const url = new URL(window.location);
            url.searchParams.set('onglet', tabName);
            
            // Préserver la recherche si elle existe
            const recherche = url.searchParams.get('recherche');
            if (recherche) {
                url.searchParams.set('recherche', recherche);
            }
            
            window.location.href = url.toString();
        }

        // Confirmation pour les actions de retour
        document.addEventListener('DOMContentLoaded', function() {
            const formsRetour = document.querySelectorAll('form[method="POST"]');
            formsRetour.forEach(form => {
                const actionInput = form.querySelector('input[name="action"]');
                if (actionInput && actionInput.value === 'retourner') {
                    form.addEventListener('submit', function(e) {
                        if (!confirm('Confirmer le retour de ce livre ?')) {
                            e.preventDefault();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>
