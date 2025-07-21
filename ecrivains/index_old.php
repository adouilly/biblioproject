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
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: #333;
            color: white;
            padding: 1rem 0;
            margin-bottom: 20px;
        }
        
        nav ul {
            list-style: none;
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        nav ul li {
            margin: 0 15px;
        }
        
        nav ul li a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        nav ul li a:hover, nav ul li a.active {
            background: #555;
        }
        
        .card {
            background: white;
            padding: 30px;
            margin: 20px 0;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 8px 16px;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            font-size: 14px;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #1e7e34;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-danger {
            background: #dc3545;
        }
        
        .btn-danger:hover {
            background: #c82333;
        }
        
        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }
        
        h1, h2, h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .alert-info {
            background: #cce7ff;
            color: #004085;
            border: 1px solid #b6d7ff;
        }
        
        .search-bar {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        
        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
        }
        
        .actions {
            display: flex;
            gap: 5px;
        }
        
        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
        }
        
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #007bff;
        }
        
        .empty-message {
            text-align: center;
            color: #666;
            font-style: italic;
            padding: 40px;
        }
        
        .header-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 style="text-align: center; margin-bottom: 20px;">✍️ Gestion des Écrivains</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">🏠 Accueil</a></li>
                    <li><a href="../livres/index.php">📖 Livres</a></li>
                    <li><a href="index.php" class="active">✍️ Écrivains</a></li>
                    <li><a href="../genres/index.php">🏷️ Genres</a></li>
                    <li><a href="../utilisateurs/index.php">👥 Utilisateurs</a></li>
                    <li><a href="../emprunts/index.php">📋 Emprunts</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($message): ?>
            <div class="alert alert-<?= $type_message ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="header-actions">
            <h2>Liste des Écrivains</h2>
            <a href="ajouter.php" class="btn btn-success">➕ Ajouter un écrivain</a>
        </div>

        <div class="search-bar">
            <form method="GET" style="display: flex; width: 100%; gap: 10px;">
                <input type="text" name="recherche" placeholder="Rechercher par nom, prénom ou nationalité..." 
                       value="<?= htmlspecialchars($recherche) ?>">
                <button type="submit" class="btn">🔍 Rechercher</button>
                <?php if ($recherche): ?>
                    <a href="index.php" class="btn btn-warning">❌ Effacer</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($ecrivains) ?></div>
                <div>Écrivain<?= count($ecrivains) > 1 ? 's' : '' ?> trouvé<?= count($ecrivains) > 1 ? 's' : '' ?></div>
            </div>
        </div>

        <div class="card">
            <?php if (empty($ecrivains)): ?>
                <div class="empty-message">
                    <?php if ($recherche): ?>
                        Aucun écrivain trouvé pour "<?= htmlspecialchars($recherche) ?>"
                    <?php else: ?>
                        Aucun écrivain enregistré pour le moment.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Nationalité</th>
                            <th>Date de naissance</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($ecrivains as $ecrivain): ?>
                            <tr>
                                <td><?= $ecrivain['id_ecrivain'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($ecrivain['prenom'] . ' ' . $ecrivain['nom']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($ecrivain['nationalite']) ?></td>
                                <td>
                                    <?php if ($ecrivain['date_naissance']): ?>
                                        <?= date('d/m/Y', strtotime($ecrivain['date_naissance'])) ?>
                                    <?php else: ?>
                                        <span style="color: #666; font-style: italic;">Non renseignée</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="modifier.php?id=<?= $ecrivain['id_ecrivain'] ?>" 
                                           class="btn btn-warning btn-sm">✏️ Modifier</a>
                                        <a href="index.php?action=supprimer&id=<?= $ecrivain['id_ecrivain'] ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet écrivain ?')">
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
