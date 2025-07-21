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
            <h1 style="text-align: center; margin-bottom: 20px;">📚 Gestion des Livres</h1>
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

        <div class="card">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2>Liste des Livres (<?= count($livres) ?>)</h2>
                <a href="ajouter.php" class="btn btn-success">+ Ajouter un livre</a>
            </div>

            <div class="search-bar">
                <form method="GET" style="display: flex; gap: 10px; width: 100%;">
                    <input type="text" name="recherche" placeholder="Rechercher par titre, auteur ou genre..." 
                           value="<?= htmlspecialchars($recherche) ?>">
                    <button type="submit" class="btn">🔍 Rechercher</button>
                    <?php if ($recherche): ?>
                        <a href="index.php" class="btn btn-warning">✖ Effacer</a>
                    <?php endif; ?>
                </form>
            </div>

            <?php if (empty($livres)): ?>
                <p>Aucun livre trouvé.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
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
                                <td><strong><?= htmlspecialchars($livre['titre']) ?></strong></td>
                                <td><?= htmlspecialchars($livre['auteur']) ?><br>
                                    <small style="color: #666;"><?= htmlspecialchars($livre['nationalite']) ?></small>
                                </td>
                                <td><?= htmlspecialchars($livre['nom_genre']) ?></td>
                                <td><?= $livre['annee_publication'] ?: '-' ?></td>
                                <td><?= htmlspecialchars($livre['isbn']) ?: '-' ?></td>
                                <td>
                                    <?php if ($livre['disponible']): ?>
                                        <span class="status-disponible">✅ Disponible</span>
                                    <?php else: ?>
                                        <span class="status-emprunte">❌ Emprunté</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="modifier.php?id=<?= $livre['id_livre'] ?>" class="btn btn-warning btn-sm">✏️ Modifier</a>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce livre ?')">
                                        <input type="hidden" name="action" value="supprimer">
                                        <input type="hidden" name="id" value="<?= $livre['id_livre'] ?>">
                                        <button type="submit" class="btn btn-danger btn-sm">🗑️ Supprimer</button>
                                    </form>
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
