<?php
require_once '../config/database.php';
require_once '../models/Emprunt.php';

$empruntModel = new Emprunt();

$message = '';
$type_message = '';

// Traitement du retour d'un livre
if (isset($_GET['action']) && $_GET['action'] === 'retourner' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        if ($empruntModel->retournerLivre($id)) {
            $message = "Livre retourné avec succès";
            $type_message = "success";
        } else {
            $message = "Erreur lors du retour du livre";
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

// Récupération du filtre de statut
$filtre_statut = $_GET['statut'] ?? 'tous';

// Récupération du terme de recherche
$recherche = $_GET['recherche'] ?? '';

// Récupération de la liste des emprunts selon les filtres
if ($recherche) {
    $emprunts = $empruntModel->rechercher($recherche);
} elseif ($filtre_statut === 'en_cours') {
    $emprunts = $empruntModel->listerEnCours();
} elseif ($filtre_statut === 'termines') {
    $emprunts = $empruntModel->listerTermines();
} elseif ($filtre_statut === 'en_retard') {
    $emprunts = $empruntModel->listerEnRetard();
} else {
    $emprunts = $empruntModel->listerTous();
}

// Calcul des statistiques
$stats = $empruntModel->obtenirStatistiques();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Emprunts - Bibliothèque</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            background-color: #f4f4f4;
        }
        
        .container {
            max-width: 1400px;
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
        
        .btn-info {
            background: #17a2b8;
        }
        
        .btn-info:hover {
            background: #138496;
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
            flex-wrap: wrap;
        }
        
        .search-bar input {
            flex: 1;
            min-width: 250px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .filter-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: white;
            font-size: 14px;
        }
        
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background: #f8f9fa;
            font-weight: bold;
            color: #333;
            font-size: 13px;
        }
        
        .actions {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
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
        }
        
        .stat-en-cours { color: #007bff; }
        .stat-en-retard { color: #dc3545; }
        .stat-termines { color: #28a745; }
        .stat-total { color: #6c757d; }
        
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
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }
        
        .status-en-cours {
            background: #cce7ff;
            color: #004085;
        }
        
        .status-en-retard {
            background: #f8d7da;
            color: #721c24;
        }
        
        .status-termine {
            background: #d4edda;
            color: #155724;
        }
        
        .urgent {
            background: #ffcccc !important;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 style="text-align: center; margin-bottom: 20px;">📋 Gestion des Emprunts</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">🏠 Accueil</a></li>
                    <li><a href="../livres/index.php">📖 Livres</a></li>
                    <li><a href="../ecrivains/index.php">✍️ Écrivains</a></li>
                    <li><a href="../genres/index.php">🏷️ Genres</a></li>
                    <li><a href="../utilisateurs/index.php">👥 Utilisateurs</a></li>
                    <li><a href="index.php" class="active">📋 Emprunts</a></li>
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
            <h2>Liste des Emprunts</h2>
            <a href="ajouter.php" class="btn btn-success">➕ Nouvel emprunt</a>
        </div>

        <div class="search-bar">
            <form method="GET" style="display: flex; width: 100%; gap: 10px; flex-wrap: wrap;">
                <input type="text" name="recherche" placeholder="Rechercher par livre, auteur ou emprunteur..." 
                       value="<?= htmlspecialchars($recherche) ?>">
                <button type="submit" class="btn">🔍 Rechercher</button>
                <?php if ($recherche): ?>
                    <a href="index.php" class="btn btn-warning">❌ Effacer</a>
                <?php endif; ?>
            </form>
            
            <div class="filter-buttons">
                <a href="index.php?statut=tous" class="btn <?= $filtre_statut === 'tous' ? 'btn-info' : '' ?>">Tous</a>
                <a href="index.php?statut=en_cours" class="btn <?= $filtre_statut === 'en_cours' ? 'btn-info' : '' ?>">En cours</a>
                <a href="index.php?statut=en_retard" class="btn <?= $filtre_statut === 'en_retard' ? 'btn-danger' : '' ?>">En retard</a>
                <a href="index.php?statut=termines" class="btn <?= $filtre_statut === 'termines' ? 'btn-success' : '' ?>">Terminés</a>
            </div>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number stat-en-cours"><?= $stats['en_cours'] ?></div>
                <div>Emprunt<?= $stats['en_cours'] > 1 ? 's' : '' ?> en cours</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-en-retard"><?= $stats['en_retard'] ?></div>
                <div>En retard</div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-termines"><?= $stats['termines'] ?></div>
                <div>Terminé<?= $stats['termines'] > 1 ? 's' : '' ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-number stat-total"><?= $stats['total'] ?></div>
                <div>Total</div>
            </div>
        </div>

        <div class="card">
            <?php if (empty($emprunts)): ?>
                <div class="empty-message">
                    <?php if ($recherche): ?>
                        Aucun emprunt trouvé pour "<?= htmlspecialchars($recherche) ?>"
                    <?php elseif ($filtre_statut !== 'tous'): ?>
                        Aucun emprunt trouvé pour le filtre "<?= ucfirst(str_replace('_', ' ', $filtre_statut)) ?>"
                    <?php else: ?>
                        Aucun emprunt enregistré pour le moment.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Livre</th>
                            <th>Auteur</th>
                            <th>Emprunteur</th>
                            <th>Date emprunt</th>
                            <th>Date prévue</th>
                            <th>Date retour</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($emprunts as $emprunt): ?>
                            <?php
                            $en_retard = $emprunt['statut'] === 'en_cours' && strtotime($emprunt['date_retour_prevue']) < time();
                            $bientot_du = $emprunt['statut'] === 'en_cours' && 
                                         strtotime($emprunt['date_retour_prevue']) - time() <= 3 * 24 * 3600 &&
                                         strtotime($emprunt['date_retour_prevue']) >= time();
                            ?>
                            <tr <?= $en_retard ? 'class="urgent"' : '' ?>>
                                <td><?= $emprunt['id_emprunt'] ?></td>
                                <td><strong><?= htmlspecialchars($emprunt['titre']) ?></strong></td>
                                <td><?= htmlspecialchars($emprunt['auteur']) ?></td>
                                <td><?= htmlspecialchars($emprunt['emprunteur']) ?></td>
                                <td><?= date('d/m/Y', strtotime($emprunt['date_emprunt'])) ?></td>
                                <td><?= date('d/m/Y', strtotime($emprunt['date_retour_prevue'])) ?></td>
                                <td>
                                    <?php if ($emprunt['date_retour_effective']): ?>
                                        <?= date('d/m/Y', strtotime($emprunt['date_retour_effective'])) ?>
                                    <?php else: ?>
                                        <span style="color: #666; font-style: italic;">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($emprunt['statut'] === 'en_cours'): ?>
                                        <?php if ($en_retard): ?>
                                            <span class="status-badge status-en-retard">⚠️ EN RETARD</span>
                                        <?php elseif ($bientot_du): ?>
                                            <span class="status-badge status-en-cours">⏰ BIENTÔT DÛ</span>
                                        <?php else: ?>
                                            <span class="status-badge status-en-cours">📖 EN COURS</span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="status-badge status-termine">✅ TERMINÉ</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <?php if ($emprunt['statut'] === 'en_cours'): ?>
                                            <a href="index.php?action=retourner&id=<?= $emprunt['id_emprunt'] ?>" 
                                               class="btn btn-success btn-sm"
                                               onclick="return confirm('Confirmer le retour de ce livre ?')">
                                               📥 Retourner
                                            </a>
                                        <?php endif; ?>
                                        <a href="modifier.php?id=<?= $emprunt['id_emprunt'] ?>" 
                                           class="btn btn-warning btn-sm">✏️ Modifier</a>
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
