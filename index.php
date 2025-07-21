<?php
require_once 'config/database.php';
require_once 'models/Livre.php';
require_once 'models/Utilisateur.php';
require_once 'models/Emprunt.php';

// Initialisation des variables
$livres = [];
$livresDisponibles = [];
$utilisateurs = [];
$empruntsEnCours = [];
$empruntsEnRetard = [];
$error_message = null;

try {
    $livreModel = new Livre();
    $utilisateurModel = new Utilisateur();
    $empruntModel = new Emprunt();

    // Récupération des données pour les statistiques
    $livres = $livreModel->listerTous();
    $livresDisponibles = $livreModel->listerDisponibles();
    $utilisateurs = $utilisateurModel->listerActifs();
    $empruntsEnCours = $empruntModel->listerEnCours();
    $empruntsEnRetard = $empruntModel->listerEnRetard();
} catch (Exception $e) {
    $error_message = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque - Gestion</title>
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
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        
        .btn {
            display: inline-block;
            background: #007bff;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
            margin: 5px 0;
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
        
        h1, h2, h3 {
            color: #333;
            margin-bottom: 15px;
        }
        
        .stats {
            text-align: center;
            font-size: 1.2em;
            color: #007bff;
        }
        
        .stats p {
            margin: 10px 0;
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
        
        .welcome {
            text-align: center;
            padding: 30px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 10px;
            margin-bottom: 30px;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1 style="text-align: center; margin-bottom: 20px;">📚 Système de Gestion de Bibliothèque</h1>
            <nav>
                <ul>
                    <li><a href="index.php" class="active">🏠 Accueil</a></li>
                    <li><a href="livres/index.php">📖 Livres</a></li>
                    <li><a href="ecrivains/index.php">✍️ Écrivains</a></li>
                    <li><a href="genres/index.php">🏷️ Genres</a></li>
                    <li><a href="utilisateurs/index.php">👥 Utilisateurs</a></li>
                    <li><a href="emprunts/index.php">📋 Emprunts</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <?php if ($error_message): ?>
        <div class="alert alert-danger">
            <h3>⚠️ Erreur de connexion à la base de données</h3>
            <p><?= htmlspecialchars($error_message) ?></p>
            <p>Veuillez vérifier que votre base de données est accessible et que les tables sont créées.</p>
        </div>
        <?php else: ?>
        
        <div class="welcome">
            <h2>Bienvenue dans votre bibliothèque</h2>
            <p>Gérez efficacement votre collection de livres, vos écrivains, utilisateurs et emprunts</p>
        </div>

        <div class="grid">
            <div class="card">
                <h3>📊 Statistiques</h3>
                <div class="stats">
                    <p><strong><?= count($livres) ?></strong> livre<?= count($livres) > 1 ? 's' : '' ?> au total</p>
                    <p><strong><?= count($livresDisponibles) ?></strong> livre<?= count($livresDisponibles) > 1 ? 's' : '' ?> disponible<?= count($livresDisponibles) > 1 ? 's' : '' ?></p>
                    <p><strong><?= count($utilisateurs) ?></strong> utilisateur<?= count($utilisateurs) > 1 ? 's' : '' ?> actif<?= count($utilisateurs) > 1 ? 's' : '' ?></p>
                    <p><strong><?= count($empruntsEnCours) ?></strong> emprunt<?= count($empruntsEnCours) > 1 ? 's' : '' ?> en cours</p>
                    <?php if (count($empruntsEnRetard) > 0): ?>
                        <p style="color: #dc3545;"><strong><?= count($empruntsEnRetard) ?></strong> emprunt<?= count($empruntsEnRetard) > 1 ? 's' : '' ?> en retard ⚠️</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3>🚀 Actions rapides</h3>
                <div>
                    <a href="livres/ajouter.php" class="btn btn-success">📖 Ajouter un livre</a>
                    <a href="emprunts/ajouter.php" class="btn btn-success">📋 Nouvel emprunt</a>
                    <a href="utilisateurs/ajouter.php" class="btn btn-success">👥 Nouvel utilisateur</a>
                    <a href="ecrivains/ajouter.php" class="btn btn-success">✍️ Nouvel écrivain</a>
                    <a href="genres/ajouter.php" class="btn btn-success">🏷️ Nouveau genre</a>
                    <a href="emprunts/index.php" class="btn btn-warning">📋 Gérer les emprunts</a>
                </div>
            </div>

            <div class="card">
                <h3>📖 Derniers livres</h3>
                <?php if (count($livres) > 0): ?>
                    <?php 
                    $derniersLivres = array_slice($livres, -5);
                    foreach ($derniersLivres as $livre): 
                    ?>
                        <p>• <?= htmlspecialchars($livre['titre']) ?> - <?= htmlspecialchars($livre['auteur']) ?></p>
                    <?php endforeach; ?>
                    <br>
                    <a href="livres/index.php" class="btn">📚 Voir tous les livres</a>
                <?php else: ?>
                    <p style="color: #666; font-style: italic;">Aucun livre enregistré pour le moment.</p>
                    <a href="livres/ajouter.php" class="btn btn-success">➕ Ajouter le premier livre</a>
                <?php endif; ?>
            </div>
        </div>

        <?php if (count($empruntsEnRetard) > 0): ?>
        <div class="alert alert-danger">
            <h3>⚠️ Emprunts en retard</h3>
            <?php foreach ($empruntsEnRetard as $emprunt): ?>
                <p>• <strong><?= htmlspecialchars($emprunt['titre']) ?></strong> emprunté par 
                   <?= htmlspecialchars($emprunt['emprunteur']) ?> 
                   (retour prévu le <?= date('d/m/Y', strtotime($emprunt['date_retour_prevue'])) ?>)</p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
    </div>
</body>
</html>
            $empruntModel = new Emprunt();

            // Statistiques
            $livres = $livreModel->listerTous();
            $livresDisponibles = $livreModel->listerDisponibles();
            $utilisateurs = $utilisateurModel->listerActifs();
            $empruntsEnCours = $empruntModel->listerEnCours();
            $empruntsEnRetard = $empruntModel->obtenirEnRetard();
        ?>

        <div class="grid">
            <div class="card">
                <h3>📊 Statistiques</h3>
                <div class="stats">
                    <p><strong><?= count($livres) ?></strong> livres au total</p>
                    <p><strong><?= count($livresDisponibles) ?></strong> livres disponibles</p>
                    <p><strong><?= count($utilisateurs) ?></strong> utilisateurs actifs</p>
                    <p><strong><?= count($empruntsEnCours) ?></strong> emprunts en cours</p>
                    <?php if (count($empruntsEnRetard) > 0): ?>
                        <p style="color: #dc3545;"><strong><?= count($empruntsEnRetard) ?></strong> emprunts en retard ⚠️</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3>🚀 Actions rapides</h3>
                <p><a href="livres/ajouter.php" class="btn btn-success">+ Ajouter un livre</a></p>
                <p><a href="emprunts/ajouter.php" class="btn btn-success">+ Nouvel emprunt</a></p>
                <p><a href="utilisateurs/ajouter.php" class="btn btn-success">+ Nouvel utilisateur</a></p>
                <p><a href="emprunts/index.php" class="btn btn-warning">📋 Gérer les emprunts</a></p>
            </div>

            <div class="card">
                <h3>📖 Derniers livres ajoutés</h3>
                <?php 
                $derniersLivres = array_slice($livres, -5);
                foreach ($derniersLivres as $livre): 
                ?>
                    <p>• <?= htmlspecialchars($livre['titre']) ?> - <?= htmlspecialchars($livre['auteur']) ?></p>
                <?php endforeach; ?>
                <p><a href="livres/index.php" class="btn">Voir tous les livres</a></p>
            </div>
        </div>

        <?php if (count($empruntsEnRetard) > 0): ?>
        <div class="alert alert-danger">
            <h3>⚠️ Emprunts en retard</h3>
            <?php foreach ($empruntsEnRetard as $emprunt): ?>
                <p>• <strong><?= htmlspecialchars($emprunt['titre']) ?></strong> emprunté par 
                   <?= htmlspecialchars($emprunt['emprunteur']) ?> 
                   (retour prévu le <?= date('d/m/Y', strtotime($emprunt['date_retour_prevue'])) ?>)</p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if (isset($error_message)): ?>
        <div class="alert alert-danger">
            <h3>Erreur de connexion à la base de données</h3>
            <p><?= htmlspecialchars($error_message) ?></p>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
