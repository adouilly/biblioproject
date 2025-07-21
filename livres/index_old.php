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
    <style>
        .search-container {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 20px;
            padding: 25px;
            margin-bottom: 30px;
        }
        
        .search-form {
            display: flex;
            gap: 15px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .search-input {
            flex: 1;
            min-width: 300px;
            padding: 12px 20px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .table-container {
            background: rgba(255, 255, 255, 0.25);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
        }
        
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .table th,
        .table td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .table th {
            background: rgba(255, 255, 255, 0.1);
            font-weight: 600;
            color: var(--text-dark);
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .table tr:hover {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .badge-success {
            background: linear-gradient(135deg, #56ab2f, #a8e6cf);
            color: white;
        }
        
        .badge-danger {
            background: linear-gradient(135deg, #fc466b, #3f5efb);
            color: white;
        }
        
        .btn-group {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        
        .btn-sm {
            padding: 8px 15px;
            font-size: 12px;
            border-radius: 10px;
        }
        
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }
        
        .page-title {
            color: var(--text-dark);
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 15px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(31, 38, 135, 0.3);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
        }
        
        .stat-label {
            font-size: 14px;
            color: var(--text-secondary);
            font-weight: 500;
        }
        
        @media (max-width: 768px) {
            .search-form {
                flex-direction: column;
            }
            
            .search-input {
                min-width: 100%;
            }
            
            .page-header {
                flex-direction: column;
                text-align: center;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .btn-group {
                justify-content: center;
            }
        }
    </style>
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
            padding: 20px;
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
            font-size: 14px;
            transition: background 0.3s;
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
            padding: 4px 8px;
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
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .search-bar {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }
        
        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        .status-disponible {
            color: #28a745;
            font-weight: bold;
        }
        
        .status-emprunte {
            color: #dc3545;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>📚 Gestion des Livres</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">🏠 Accueil</a></li>
                    <li><a href="index.php" class="active">📖 Livres</a></li>
                    <li><a href="../ecrivains/index.php">✍️ Écrivains</a></li>
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

        <div class="page-header">
            <h2 class="page-title">📚 Bibliothèque Digitale</h2>
            <a href="ajouter.php" class="btn btn-success">✨ Ajouter un livre</a>
        </div>

        <!-- Statistiques -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number"><?= count($livres) ?></div>
                <div class="stat-label">📚 Total livres</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($livres, fn($l) => $l['disponible'])) ?></div>
                <div class="stat-label">✅ Disponibles</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= count(array_filter($livres, fn($l) => !$l['disponible'])) ?></div>
                <div class="stat-label">📋 Empruntés</div>
            </div>
        </div>

        <!-- Recherche -->
        <div class="search-container">
            <form method="GET" class="search-form">
                <input type="text" 
                       name="recherche" 
                       class="search-input" 
                       placeholder="🔍 Rechercher un livre, auteur ou genre..." 
                       value="<?= htmlspecialchars($recherche) ?>">
                <button type="submit" class="btn">🔍 Rechercher</button>
                <?php if ($recherche): ?>
                    <a href="index.php" class="btn btn-warning">🗑️ Effacer</a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Tableau des livres -->
        <div class="table-container">
            <?php if (empty($livres)): ?>
                <div style="padding: 40px; text-align: center; color: var(--text-secondary);">
                    <div style="font-size: 3rem; margin-bottom: 20px;">📚</div>
                    <h3>Aucun livre trouvé</h3>
                    <p>Commencez par ajouter votre premier livre à la collection</p>
                    <br>
                    <a href="ajouter.php" class="btn btn-success">✨ Ajouter un livre</a>
                </div>
            <?php else: ?>
                <table class="glass-table">
                    <thead>
                        <tr>
                            <th>📖 Titre</th>
                            <th>✍️ Auteur</th>
                            <th>🏷️ Genre</th>
                            <th>📅 Année</th>
                            <th>🔢 ISBN</th>
                            <th>📋 Statut</th>
                            <th>⚙️ Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($livres as $livre): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--text-dark);"><?= htmlspecialchars($livre['titre']) ?></strong>
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
