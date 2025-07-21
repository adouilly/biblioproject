<?php
require_once '../config/database.php';
require_once '../models/Livre.php';
require_once '../models/Ecrivain.php';
require_once '../models/Genre.php';

$livreModel = new Livre();
$ecrivainModel = new Ecrivain();
$genreModel = new Genre();

$message = '';
$type_message = '';

// Récupération des listes pour les selects
$ecrivains = $ecrivainModel->listerTous();
$genres = $genreModel->listerTous();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $titre = trim($_POST['titre']);
        $annee_publication = !empty($_POST['annee_publication']) ? (int)$_POST['annee_publication'] : null;
        $isbn = trim($_POST['isbn']) ?: null;
        $id_ecrivain = (int)$_POST['id_ecrivain'];
        $id_genre = (int)$_POST['id_genre'];

        // Validations
        if (empty($titre)) {
            throw new Exception("Le titre est obligatoire");
        }
        if ($id_ecrivain <= 0) {
            throw new Exception("Veuillez sélectionner un écrivain");
        }
        if ($id_genre <= 0) {
            throw new Exception("Veuillez sélectionner un genre");
        }
        if ($annee_publication && ($annee_publication < 1 || $annee_publication > date('Y'))) {
            throw new Exception("L'année de publication doit être valide");
        }

        if ($livreModel->ajouter($titre, $annee_publication, $isbn, $id_ecrivain, $id_genre)) {
            header('Location: index.php?message=Livre ajouté avec succès&type=success');
            exit;
        } else {
            throw new Exception("Erreur lors de l'ajout du livre");
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
    <title>Ajouter un Livre - Bibliothèque</title>
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
        
        .btn-success {
            background: #28a745;
        }
        
        .btn-success:hover {
            background: #1e7e34;
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
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 style="text-align: center; margin-bottom: 20px;">➕ Ajouter un Livre</h1>
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
            <h2>Nouveau Livre</h2>
            
            <form method="POST">
                <div class="form-group">
                    <label for="titre">Titre <span class="required">*</span></label>
                    <input type="text" id="titre" name="titre" required 
                           value="<?= isset($_POST['titre']) ? htmlspecialchars($_POST['titre']) : '' ?>">
                </div>

                <div class="form-group">
                    <label for="id_ecrivain">Écrivain <span class="required">*</span></label>
                    <select id="id_ecrivain" name="id_ecrivain" required>
                        <option value="">-- Sélectionner un écrivain --</option>
                        <?php foreach ($ecrivains as $ecrivain): ?>
                            <option value="<?= $ecrivain['id_ecrivain'] ?>"
                                    <?= (isset($_POST['id_ecrivain']) && $_POST['id_ecrivain'] == $ecrivain['id_ecrivain']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($ecrivain['prenom'] . ' ' . $ecrivain['nom']) ?>
                                (<?= htmlspecialchars($ecrivain['nationalite']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_genre">Genre <span class="required">*</span></label>
                    <select id="id_genre" name="id_genre" required>
                        <option value="">-- Sélectionner un genre --</option>
                        <?php foreach ($genres as $genre): ?>
                            <option value="<?= $genre['id_genre'] ?>"
                                    <?= (isset($_POST['id_genre']) && $_POST['id_genre'] == $genre['id_genre']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($genre['nom_genre']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="annee_publication">Année de publication</label>
                    <input type="number" id="annee_publication" name="annee_publication" 
                           min="1" max="<?= date('Y') ?>"
                           value="<?= isset($_POST['annee_publication']) ? $_POST['annee_publication'] : '' ?>">
                </div>

                <div class="form-group">
                    <label for="isbn">ISBN</label>
                    <input type="text" id="isbn" name="isbn" 
                           placeholder="Ex: 978-2-07-040570-1"
                           value="<?= isset($_POST['isbn']) ? htmlspecialchars($_POST['isbn']) : '' ?>">
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-success">💾 Enregistrer</button>
                    <a href="index.php" class="btn btn-secondary">❌ Annuler</a>
                </div>
            </form>
        </div>

        <?php if (empty($ecrivains) || empty($genres)): ?>
            <div class="alert alert-danger">
                <h4>Attention !</h4>
                <?php if (empty($ecrivains)): ?>
                    <p>• Aucun écrivain trouvé. <a href="../ecrivains/ajouter.php">Ajouter un écrivain</a> d'abord.</p>
                <?php endif; ?>
                <?php if (empty($genres)): ?>
                    <p>• Aucun genre trouvé. <a href="../genres/ajouter.php">Ajouter un genre</a> d'abord.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
