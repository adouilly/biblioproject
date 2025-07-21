<?php
require_once '../config/database.php';
require_once '../models/Ecrivain.php';

$ecrivainModel = new Ecrivain();

$message = '';
$type_message = '';

// Vérification de l'ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?message=Écrivain introuvable&type=danger');
    exit;
}

$id_ecrivain = (int)$_GET['id'];

// Récupération de l'écrivain
$ecrivain = $ecrivainModel->obtenirParId($id_ecrivain);
if (!$ecrivain) {
    header('Location: index.php?message=Écrivain introuvable&type=danger');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $nationalite = trim($_POST['nationalite']);
        $date_naissance = !empty($_POST['date_naissance']) ? $_POST['date_naissance'] : null;

        // Validations
        if (empty($nom)) {
            throw new Exception("Le nom est obligatoire");
        }
        if (empty($prenom)) {
            throw new Exception("Le prénom est obligatoire");
        }
        if (empty($nationalite)) {
            throw new Exception("La nationalité est obligatoire");
        }
        if ($date_naissance && strtotime($date_naissance) > time()) {
            throw new Exception("La date de naissance ne peut pas être dans le futur");
        }

        if ($ecrivainModel->modifier($id_ecrivain, $nom, $prenom, $nationalite, $date_naissance)) {
            header('Location: index.php?message=Écrivain modifié avec succès&type=success');
            exit;
        } else {
            throw new Exception("Erreur lors de la modification de l'écrivain");
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
    <title>Modifier un Écrivain - Bibliothèque</title>
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
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        @media (max-width: 768px) {
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 style="text-align: center; margin-bottom: 20px;">✏️ Modifier un Écrivain</h1>
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

        <div class="card">
            <div class="info-box">
                <strong>Écrivain à modifier :</strong> <?= htmlspecialchars($ecrivain['prenom'] . ' ' . $ecrivain['nom']) ?><br>
                <strong>Nationalité actuelle :</strong> <?= htmlspecialchars($ecrivain['nationalite']) ?>
            </div>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="prenom">Prénom <span class="required">*</span></label>
                        <input type="text" id="prenom" name="prenom" required 
                               value="<?= isset($_POST['prenom']) ? htmlspecialchars($_POST['prenom']) : htmlspecialchars($ecrivain['prenom']) ?>">
                    </div>

                    <div class="form-group">
                        <label for="nom">Nom <span class="required">*</span></label>
                        <input type="text" id="nom" name="nom" required 
                               value="<?= isset($_POST['nom']) ? htmlspecialchars($_POST['nom']) : htmlspecialchars($ecrivain['nom']) ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="nationalite">Nationalité <span class="required">*</span></label>
                    <input type="text" id="nationalite" name="nationalite" required 
                           placeholder="Ex: Française, Américaine, etc."
                           value="<?= isset($_POST['nationalite']) ? htmlspecialchars($_POST['nationalite']) : htmlspecialchars($ecrivain['nationalite']) ?>">
                </div>

                <div class="form-group">
                    <label for="date_naissance">Date de naissance</label>
                    <input type="date" id="date_naissance" name="date_naissance" 
                           max="<?= date('Y-m-d') ?>"
                           value="<?= isset($_POST['date_naissance']) ? $_POST['date_naissance'] : $ecrivain['date_naissance'] ?>">
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
