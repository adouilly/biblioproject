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
        $livres_ids = isset($_POST['id_livre']) ? $_POST['id_livre'] : [];
        $id_utilisateur = (int)$_POST['id_utilisateur'];
        $date_emprunt = $_POST['date_emprunt'];
        $duree_emprunt = (int)$_POST['duree_emprunt'];
        $remarques = trim($_POST['remarques']) ?: null;

        // Validations
        if (empty($livres_ids) || !is_array($livres_ids)) {
            throw new Exception("Veuillez sélectionner au moins un livre");
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

        $emprunts_crees = 0;
        $erreurs = [];

        // Créer un emprunt pour chaque livre sélectionné
        foreach ($livres_ids as $id_livre) {
            $id_livre = (int)$id_livre;
            try {
                if ($empruntModel->ajouter($id_livre, $id_utilisateur, $date_emprunt, $date_retour_prevue, $remarques)) {
                    $emprunts_crees++;
                } else {
                    $erreurs[] = "Erreur lors de la création de l'emprunt pour le livre ID $id_livre";
                }
            } catch (Exception $e) {
                $erreurs[] = "Livre ID $id_livre : " . $e->getMessage();
            }
        }

        if ($emprunts_crees > 0) {
            $message_succes = "✅ $emprunts_crees emprunt(s) créé(s) avec succès";
            if (!empty($erreurs)) {
                $message_succes .= " (quelques erreurs : " . implode(', ', $erreurs) . ")";
            }
            header('Location: index.php?message=' . urlencode($message_succes) . '&type=success');
            exit;
        } else {
            throw new Exception("Aucun emprunt n'a pu être créé : " . implode(', ', $erreurs));
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
        
        /* Styles pour la sélection de livres avec cases à cocher */
        .books-selection-container {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 15px;
            background: #f8f9fa;
            max-height: 400px;
            overflow-y: auto;
        }
        
        .books-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 10px;
        }
        
        .book-card {
            background: white;
            border: 2px solid #dee2e6;
            border-radius: 8px;
            padding: 12px;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .book-card:hover {
            border-color: #007bff;
            box-shadow: 0 2px 8px rgba(0,123,255,0.2);
            transform: translateY(-2px);
        }
        
        .book-card.selected {
            border-color: #007bff;
            background: #e3f2fd;
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }
        
        .book-checkbox-label {
            display: flex;
            align-items: flex-start;
            cursor: pointer;
            width: 100%;
            margin: 0;
        }
        
        .book-checkbox {
            width: auto !important;
            margin-right: 10px;
            margin-top: 4px;
            transform: scale(1.2);
        }
        
        .book-info {
            flex: 1;
        }
        
        .book-title {
            font-weight: bold;
            color: #333;
            margin-bottom: 4px;
            font-size: 14px;
        }
        
        .book-author {
            color: #666;
            font-size: 13px;
            margin-bottom: 2px;
        }
        
        .book-genre {
            color: #888;
            font-size: 12px;
        }
        
        .selected-count {
            margin-top: 10px;
            padding: 8px 12px;
            background: #d4edda;
            border: 1px solid #c3e6cb;
            border-radius: 4px;
            color: #155724;
        }
        
        .count-badge {
            background: #28a745;
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: bold;
            font-size: 12px;
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
                    <label for="livres_selection">Livres à emprunter <span class="required">*</span></label>
                    <div class="books-selection-container" <?= empty($livres_disponibles) ? 'style="opacity: 0.5; pointer-events: none;"' : '' ?>>
                        <?php if (empty($livres_disponibles)): ?>
                            <p style="color: #666; font-style: italic;">Aucun livre disponible pour l'emprunt</p>
                        <?php else: ?>
                            <div class="books-grid">
                                <?php foreach ($livres_disponibles as $livre): ?>
                                    <div class="book-card">
                                        <label class="book-checkbox-label">
                                            <input type="checkbox" 
                                                   name="id_livre[]" 
                                                   value="<?= $livre['id_livre'] ?>"
                                                   class="book-checkbox"
                                                   <?= (isset($_POST['id_livre']) && in_array($livre['id_livre'], $_POST['id_livre'])) ? 'checked' : '' ?>>
                                            <div class="book-info">
                                                <div class="book-title">📖 <?= htmlspecialchars($livre['titre']) ?></div>
                                                <div class="book-author">✍️ <?= htmlspecialchars($livre['auteur']) ?></div>
                                                <div class="book-genre">🏷️ <?= htmlspecialchars($livre['nom_genre']) ?></div>
                                            </div>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="help-text">
                        💡 <strong>Cochez les livres</strong> que vous souhaitez emprunter pour cet utilisateur.<br>
                        📚 Vous pouvez sélectionner plusieurs livres qui auront les mêmes conditions d'emprunt.
                    </div>
                    <div id="selected-books-count" class="selected-count" style="display: none;">
                        <span class="count-badge">0</span> livre(s) sélectionné(s)
                    </div>
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
        
        // Gestion des cases à cocher pour la sélection de livres
        const checkboxes = document.querySelectorAll('.book-checkbox');
        const selectedCount = document.getElementById('selected-books-count');
        const countBadge = selectedCount.querySelector('.count-badge');
        
        function updateBookSelection() {
            const selected = document.querySelectorAll('.book-checkbox:checked');
            const count = selected.length;
            
            // Mise à jour du compteur
            countBadge.textContent = count;
            selectedCount.style.display = count > 0 ? 'block' : 'none';
            
            // Mise à jour visuelle des cartes
            checkboxes.forEach(checkbox => {
                const card = checkbox.closest('.book-card');
                if (checkbox.checked) {
                    card.classList.add('selected');
                } else {
                    card.classList.remove('selected');
                }
            });
        }
        
        // Événements pour les cases à cocher
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateBookSelection);
            
            // Permettre de cliquer sur toute la carte pour cocher/décocher
            const card = checkbox.closest('.book-card');
            card.addEventListener('click', function(e) {
                if (e.target !== checkbox) {
                    checkbox.checked = !checkbox.checked;
                    updateBookSelection();
                }
            });
        });
        
        // État initial
        updateBookSelection();
        
        // Validation du formulaire
        document.getElementById('empruntForm').addEventListener('submit', function(e) {
            const selectedBooks = document.querySelectorAll('.book-checkbox:checked');
            if (selectedBooks.length === 0) {
                e.preventDefault();
                alert('⚠️ Veuillez sélectionner au moins un livre à emprunter.');
                return false;
            }
            
            if (selectedBooks.length > 5) {
                if (!confirm(`📚 Vous avez sélectionné ${selectedBooks.length} livres. Êtes-vous sûr de vouloir créer autant d'emprunts simultanément ?`)) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    </script>
</body>
</html>
