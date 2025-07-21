<?php
require_once '../config/database.php';
require_once '../models/Emprunt.php';

$empruntModel = new Emprunt();

$message = '';
$type_message = '';

// Vérification de l'ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: index.php?message=Emprunt introuvable&type=danger');
    exit;
}

$id_emprunt = (int)$_GET['id'];

// Récupération de l'emprunt
$emprunt = $empruntModel->obtenirParId($id_emprunt);
if (!$emprunt) {
    header('Location: index.php?message=Emprunt introuvable&type=danger');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $date_emprunt = $_POST['date_emprunt'];
        $date_retour_prevue = $_POST['date_retour_prevue'];
        $date_retour_effective = !empty($_POST['date_retour_effective']) ? $_POST['date_retour_effective'] : null;
        $remarques = trim($_POST['remarques']) ?: null;

        // Validations
        if (empty($date_emprunt)) {
            throw new Exception("La date d'emprunt est obligatoire");
        }
        if (empty($date_retour_prevue)) {
            throw new Exception("La date de retour prévue est obligatoire");
        }
        if ($date_retour_prevue < $date_emprunt) {
            throw new Exception("La date de retour prévue ne peut pas être antérieure à la date d'emprunt");
        }
        if ($date_retour_effective && $date_retour_effective < $date_emprunt) {
            throw new Exception("La date de retour effective ne peut pas être antérieure à la date d'emprunt");
        }

        if ($empruntModel->modifier($id_emprunt, $date_emprunt, $date_retour_prevue, $date_retour_effective, $remarques)) {
            header('Location: index.php?message=Emprunt modifié avec succès&type=success');
            exit;
        } else {
            throw new Exception("Erreur lors de la modification de l'emprunt");
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
    <title>Modifier un Emprunt - Bibliothèque</title>
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
            max-width: 900px;
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
            height: 100px;
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
            padding: 20px;
            border-radius: 4px;
            margin-bottom: 20px;
        }
        
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        
        .help-text {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        
        .status-info {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
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
            <h1 style="text-align: center; margin-bottom: 20px;">✏️ Modifier un Emprunt</h1>
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

        <div class="card">
            <div class="info-box">
                <h3>Informations de l'emprunt</h3>
                <strong>Livre :</strong> <?= htmlspecialchars($emprunt['titre']) ?><br>
                <strong>Auteur :</strong> <?= htmlspecialchars($emprunt['auteur']) ?><br>
                <strong>Emprunteur :</strong> <?= htmlspecialchars($emprunt['emprunteur']) ?><br>
                <strong>Statut actuel :</strong> 
                <?php if ($emprunt['statut'] === 'en_cours'): ?>
                    <span style="color: #007bff;">📖 En cours</span>
                <?php else: ?>
                    <span style="color: #28a745;">✅ Terminé</span>
                <?php endif; ?>
            </div>

            <?php
            $en_retard = $emprunt['statut'] === 'en_cours' && strtotime($emprunt['date_retour_prevue']) < time();
            if ($en_retard):
            ?>
            <div class="status-info" style="border-left-color: #dc3545; background: #fff5f5;">
                <strong style="color: #dc3545;">⚠️ Cet emprunt est en retard !</strong><br>
                Le livre devait être retourné le <?= date('d/m/Y', strtotime($emprunt['date_retour_prevue'])) ?>.
            </div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-row">
                    <div class="form-group">
                        <label for="date_emprunt">Date d'emprunt <span class="required">*</span></label>
                        <input type="date" id="date_emprunt" name="date_emprunt" required 
                               value="<?= isset($_POST['date_emprunt']) ? $_POST['date_emprunt'] : $emprunt['date_emprunt'] ?>">
                    </div>

                    <div class="form-group">
                        <label for="date_retour_prevue">Date de retour prévue <span class="required">*</span></label>
                        <input type="date" id="date_retour_prevue" name="date_retour_prevue" required 
                               value="<?= isset($_POST['date_retour_prevue']) ? $_POST['date_retour_prevue'] : $emprunt['date_retour_prevue'] ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="date_retour_effective">Date de retour effective</label>
                    <input type="date" id="date_retour_effective" name="date_retour_effective" 
                           value="<?= isset($_POST['date_retour_effective']) ? $_POST['date_retour_effective'] : $emprunt['date_retour_effective'] ?>">
                    <div class="help-text">
                        Laisser vide si le livre n'est pas encore retourné. 
                        <?php if ($emprunt['statut'] === 'en_cours'): ?>
                            Remplir cette date marquera l'emprunt comme terminé.
                        <?php endif; ?>
                    </div>
                </div>

                <div class="form-group">
                    <label for="remarques">Remarques</label>
                    <textarea id="remarques" name="remarques" 
                              placeholder="Remarques sur cet emprunt..."><?= isset($_POST['remarques']) ? htmlspecialchars($_POST['remarques']) : htmlspecialchars($emprunt['remarques']) ?></textarea>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-warning">💾 Modifier</button>
                    <a href="index.php" class="btn btn-secondary">❌ Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validation côté client
        document.querySelector('form').addEventListener('submit', function(e) {
            const dateEmprunt = new Date(document.getElementById('date_emprunt').value);
            const dateRetourPrevue = new Date(document.getElementById('date_retour_prevue').value);
            const dateRetourEffective = document.getElementById('date_retour_effective').value;
            
            if (dateRetourPrevue < dateEmprunt) {
                e.preventDefault();
                alert('La date de retour prévue ne peut pas être antérieure à la date d\'emprunt.');
                return;
            }
            
            if (dateRetourEffective) {
                const dateEffective = new Date(dateRetourEffective);
                if (dateEffective < dateEmprunt) {
                    e.preventDefault();
                    alert('La date de retour effective ne peut pas être antérieure à la date d\'emprunt.');
                    return;
                }
            }
        });
    </script>
</body>
</html>
