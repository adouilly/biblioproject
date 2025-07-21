<?php
require_once '../config/database.php';
require_once '../models/Emprunt.php';
require_once '../models/Livre.php';
require_once '../models/Utilisateur.php';

$empruntModel = new Emprunt();
$livreModel = new Livre();
$utilisateurModel = new Utilisateur();

$message = '';
$type_message = '';

// Récupération des listes pour les selects
$livres_disponibles = $livreModel->listerDisponibles();
$utilisateurs_actifs = $utilisateurModel->listerActifs();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $id_livre = (int)$_POST['id_livre'];
        $id_utilisateur = (int)$_POST['id_utilisateur'];
        $date_emprunt = $_POST['date_emprunt'];
        $duree_emprunt = (int)$_POST['duree_emprunt'];
        $remarques = trim($_POST['remarques']) ?: null;

        // Validations
        if ($id_livre <= 0) {
            throw new Exception("Veuillez sélectionner un livre");
        }
        if ($id_utilisateur <= 0) {
            throw new Exception("Veuillez sélectionner un utilisateur");
        }
        if (empty($date_emprunt)) {
            throw new Exception("La date d'emprunt est obligatoire");
        }
        if ($duree_emprunt < 1 || $duree_emprunt > 90) {
            throw new Exception("La durée d'emprunt doit être entre 1 et 90 jours");
        }

        // Calcul de la date de retour prévue
        $date_retour_prevue = date('Y-m-d', strtotime($date_emprunt . ' + ' . $duree_emprunt . ' days'));

        if ($empruntModel->ajouter($id_livre, $id_utilisateur, $date_emprunt, $date_retour_prevue, $remarques)) {
            header('Location: index.php?message=Emprunt créé avec succès&type=success');
            exit;
        } else {
            throw new Exception("Erreur lors de la création de l'emprunt");
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
    <title>Nouvel Emprunt - Bibliothèque</title>
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
        
        .alert-warning {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
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
        
        .duration-selector {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 10px;
            margin-top: 10px;
        }
        
        .duration-btn {
            padding: 8px 12px;
            border: 2px solid #ddd;
            background: white;
            border-radius: 4px;
            cursor: pointer;
            text-align: center;
            transition: all 0.3s;
        }
        
        .duration-btn:hover {
            border-color: #007bff;
            background: #f8f9fa;
        }
        
        .duration-btn.selected {
            border-color: #007bff;
            background: #007bff;
            color: white;
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
            <h1 style="text-align: center; margin-bottom: 20px;">➕ Nouvel Emprunt</h1>
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

        <?php if (empty($livres_disponibles)): ?>
            <div class="alert alert-warning">
                <strong>Aucun livre disponible !</strong> Tous les livres sont actuellement empruntés. 
                <a href="../livres/ajouter.php">Ajouter de nouveaux livres</a> ou attendre des retours.
            </div>
        <?php elseif (empty($utilisateurs_actifs)): ?>
            <div class="alert alert-warning">
                <strong>Aucun utilisateur actif !</strong> Aucun utilisateur ne peut emprunter de livres. 
                <a href="../utilisateurs/ajouter.php">Ajouter de nouveaux utilisateurs</a> ou activer des comptes existants.
            </div>
        <?php endif; ?>

        <div class="card">
            <h2>Informations de l'emprunt</h2>
            
            <form method="POST" id="empruntForm">
                <div class="form-group">
                    <label for="id_livre">Livre à emprunter <span class="required">*</span></label>
                    <select id="id_livre" name="id_livre" required <?= empty($livres_disponibles) ? 'disabled' : '' ?>>
                        <option value="">-- Sélectionner un livre --</option>
                        <?php foreach ($livres_disponibles as $livre): ?>
                            <option value="<?= $livre['id_livre'] ?>"
                                    <?= (isset($_POST['id_livre']) && $_POST['id_livre'] == $livre['id_livre']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($livre['titre']) ?> 
                                - <?= htmlspecialchars($livre['auteur']) ?>
                                (<?= htmlspecialchars($livre['nom_genre']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="id_utilisateur">Emprunteur <span class="required">*</span></label>
                    <select id="id_utilisateur" name="id_utilisateur" required <?= empty($utilisateurs_actifs) ? 'disabled' : '' ?>>
                        <option value="">-- Sélectionner un utilisateur --</option>
                        <?php foreach ($utilisateurs_actifs as $utilisateur): ?>
                            <option value="<?= $utilisateur['id_utilisateur'] ?>"
                                    <?= (isset($_POST['id_utilisateur']) && $_POST['id_utilisateur'] == $utilisateur['id_utilisateur']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($utilisateur['prenom'] . ' ' . $utilisateur['nom']) ?>
                                (<?= htmlspecialchars($utilisateur['email']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="date_emprunt">Date d'emprunt <span class="required">*</span></label>
                        <input type="date" id="date_emprunt" name="date_emprunt" required 
                               value="<?= isset($_POST['date_emprunt']) ? $_POST['date_emprunt'] : date('Y-m-d') ?>"
                               max="<?= date('Y-m-d') ?>">
                    </div>

                    <div class="form-group">
                        <label for="duree_emprunt">Durée d'emprunt (jours) <span class="required">*</span></label>
                        <input type="number" id="duree_emprunt" name="duree_emprunt" required 
                               min="1" max="90" 
                               value="<?= isset($_POST['duree_emprunt']) ? $_POST['duree_emprunt'] : '15' ?>">
                        <div class="help-text">Entre 1 et 90 jours</div>
                        
                        <div class="duration-selector">
                            <div class="duration-btn" onclick="setDuration(7)">7 jours</div>
                            <div class="duration-btn selected" onclick="setDuration(15)">15 jours</div>
                            <div class="duration-btn" onclick="setDuration(21)">21 jours</div>
                            <div class="duration-btn" onclick="setDuration(30)">30 jours</div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="remarques">Remarques</label>
                    <textarea id="remarques" name="remarques" 
                              placeholder="Remarques optionnelles sur cet emprunt..."><?= isset($_POST['remarques']) ? htmlspecialchars($_POST['remarques']) : '' ?></textarea>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-success" 
                            <?= (empty($livres_disponibles) || empty($utilisateurs_actifs)) ? 'disabled' : '' ?>>
                        💾 Créer l'emprunt
                    </button>
                    <a href="index.php" class="btn btn-secondary">❌ Annuler</a>
                </div>
            </form>
        </div>
    </div>

    <script>
        function setDuration(days) {
            document.getElementById('duree_emprunt').value = days;
            
            // Mise à jour visuelle des boutons
            document.querySelectorAll('.duration-btn').forEach(btn => {
                btn.classList.remove('selected');
            });
            event.target.classList.add('selected');
        }

        // Mise à jour automatique de la date de retour prévue
        function updateReturnDate() {
            const dateEmprunt = document.getElementById('date_emprunt').value;
            const dureeEmprunt = document.getElementById('duree_emprunt').value;
            
            if (dateEmprunt && dureeEmprunt) {
                const dateRetour = new Date(dateEmprunt);
                dateRetour.setDate(dateRetour.getDate() + parseInt(dureeEmprunt));
                
                const options = { year: 'numeric', month: 'long', day: 'numeric' };
                const dateRetourStr = dateRetour.toLocaleDateString('fr-FR', options);
                
                // Affichage de la date de retour (optionnel)
                console.log('Date de retour prévue:', dateRetourStr);
            }
        }

        document.getElementById('date_emprunt').addEventListener('change', updateReturnDate);
        document.getElementById('duree_emprunt').addEventListener('input', updateReturnDate);
    </script>
</body>
</html>
