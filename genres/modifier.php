<?php
require_once '../config/database.php';
require_once '../models/Genre.php';

$genreModel = new Genre();

$message = '';
$type_message = '';

// Vérification de l'ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?message=Genre introuvable&type=danger');
    exit;
}

$id_genre = (int)$_GET['id'];

// Récupération du genre
$genre = $genreModel->obtenirParId($id_genre);
if (!$genre) {
    header('Location: index.php?message=Genre introuvable&type=danger');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nom_genre = trim($_POST['nom_genre']);
        $description = trim($_POST['description']) ?: null;

        // Validations
        if (empty($nom_genre)) {
            throw new Exception("Le nom du genre est obligatoire");
        }

        if ($genreModel->modifier($id_genre, $nom_genre, $description)) {
            header('Location: index.php?message=Genre modifié avec succès&type=success');
            exit;
        } else {
            throw new Exception("Erreur lors de la modification du genre");
        }
    } catch (Exception $e) {
        $message = $e->getMessage();
        $type_message = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Genre - Bibliothèque</title>
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
            max-width: 800px;
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
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #0056b3;
        }
        
        .btn-warning {
            background: #ffc107;
            color: #212529;
        }
        
        .btn-warning:hover {
            background: #e0a800;
        }
        
        .btn-secondary {
            background: #6c757d;
        }
        
        .btn-secondary:hover {
            background: #545b62;
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
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        
        textarea {
            resize: vertical;
            height: 120px;
        }
        
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0,123,255,0.3);
        }
        
        .required {
            color: #dc3545;
        }
        
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
        }
        
        .info-box {
            background: #e7f3ff;
            border: 1px solid #bee5eb;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 style="text-align: center; margin-bottom: 20px;">✏️ Modifier un Genre</h1>
            <nav>
                <ul>
                    <li><a href="../index.php">🏠 Accueil</a></li>
                    <li><a href="../livres/index.php">📖 Livres</a></li>
                    <li><a href="../ecrivains/index.php">✍️ Écrivains</a></li>
                    <li><a href="index.php" class="active">🏷️ Genres</a></li>
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
            <div class="info-box">
                <strong>Genre à modifier :</strong> <?= htmlspecialchars($genre['nom_genre']) ?>
            </div>
            
            <form method="POST">
                <div class="form-group">
                    <label for="nom_genre">Nom du genre <span class="required">*</span></label>
                    <input type="text" id="nom_genre" name="nom_genre" required 
                           placeholder="Ex: Roman, Science-fiction, Policier..."
                           value="<?= isset($_POST['nom_genre']) ? htmlspecialchars($_POST['nom_genre']) : htmlspecialchars($genre['nom_genre']) ?>">
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" 
                              placeholder="Description optionnelle du genre..."><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : htmlspecialchars($genre['description']) ?></textarea>
                    <div class="help-text">
                        Vous pouvez ajouter une description pour mieux caractériser ce genre littéraire.
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-warning">💾 Modifier</button>
                    <a href="index.php" class="btn btn-secondary">❌ Annuler</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
