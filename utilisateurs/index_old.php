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

// Traitement de l'activation/désactivation
if (isset($_GET['action']) && $_GET['action'] === 'toggle_actif' && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    try {
        if ($utilisateurModel->changerStatutActif($id)) {
            $message = "Statut de l'utilisateur modifié avec succès";
            $type_message = "success";
        } else {
            $message = "Erreur lors de la modification du statut";
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
        
        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }
        
        .status-actif {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactif {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 style="text-align: center; margin-bottom: 20px;">👥 Gestion des Utilisateurs</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">🏠 Accueil</a></li>
                    <li><a href="../livres/index.php">📖 Livres</a></li>
                    <li><a href="../ecrivains/index.php">✍️ Écrivains</a></li>
                    <li><a href="../genres/index.php">🏷️ Genres</a></li>
                    <li><a href="index.php" class="active">👥 Utilisateurs</a></li>
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
            <h2>Liste des Utilisateurs</h2>
            <a href="ajouter.php" class="btn btn-success">➕ Ajouter un utilisateur</a>
        </div>

        <div class="search-bar">
            <form method="GET" style="display: flex; width: 100%; gap: 10px;">
                <input type="text" name="recherche" placeholder="Rechercher par nom, prénom ou email..." 
                       value="<?= htmlspecialchars($recherche) ?>">
                <button type="submit" class="btn">🔍 Rechercher</button>
                <?php if ($recherche): ?>
                    <a href="index.php" class="btn btn-warning">❌ Effacer</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= count($utilisateurs) ?></div>
                <div>Utilisateur<?= count($utilisateurs) > 1 ? 's' : '' ?> trouvé<?= count($utilisateurs) > 1 ? 's' : '' ?></div>
            </div>
            <?php 
            $actifs = array_filter($utilisateurs, function($u) { return $u['actif'] == 1; });
            $inactifs = array_filter($utilisateurs, function($u) { return $u['actif'] == 0; });
            ?>
            <div class="stat-card">
                <div class="stat-number" style="color: #28a745;"><?= count($actifs) ?></div>
                <div>Actif<?= count($actifs) > 1 ? 's' : '' ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-number" style="color: #dc3545;"><?= count($inactifs) ?></div>
                <div>Inactif<?= count($inactifs) > 1 ? 's' : '' ?></div>
            </div>
        </div>

        <div class="card">
            <?php if (empty($utilisateurs)): ?>
                <div class="empty-message">
                    <?php if ($recherche): ?>
                        Aucun utilisateur trouvé pour "<?= htmlspecialchars($recherche) ?>"
                    <?php else: ?>
                        Aucun utilisateur enregistré pour le moment.
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom complet</th>
                            <th>Email</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($utilisateurs as $utilisateur): ?>
                            <tr>
                                <td><?= $utilisateur['id_utilisateur'] ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?></strong>
                                </td>
                                <td><?= htmlspecialchars($utilisateur['email']) ?></td>
                                <td>
                                    <?php if ($utilisateur['actif']): ?>
                                        <span class="status-badge status-actif">✅ Actif</span>
                                    <?php else: ?>
                                        <span class="status-badge status-inactif">❌ Inactif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="actions">
                                        <a href="modifier.php?id=<?= $utilisateur['id_utilisateur'] ?>" 
                                           class="btn btn-warning btn-sm">✏️ Modifier</a>
                                        <a href="index.php?action=toggle_actif&id=<?= $utilisateur['id_utilisateur'] ?>" 
                                           class="btn btn-info btn-sm">
                                           <?= $utilisateur['actif'] ? '🔒 Désactiver' : '🔓 Activer' ?>
                                        </a>
                                        <a href="index.php?action=supprimer&id=<?= $utilisateur['id_utilisateur'] ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
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
