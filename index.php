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
    <link rel="stylesheet" href="assets/css/glassmorphism.css">
</head>
<body>
    <header>
        <div class="container">
            <h1>📚 Système de Gestion de Bibliothèque</h1>
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
            <h2>✨ Bienvenue dans votre bibliothèque</h2>
            <p>Gérez efficacement votre collection de livres, vos écrivains, utilisateurs et emprunts avec style</p>
        </div>

        <div class="grid">
            <div class="card">
                <h3>📊 Statistiques en temps réel</h3>
                <div class="stats">
                    <p><span class="stats-icon">📚</span><strong><?= count($livres) ?></strong> livre<?= count($livres) > 1 ? 's' : '' ?> au total</p>
                    <p><span class="stats-icon">✅</span><strong><?= count($livresDisponibles) ?></strong> livre<?= count($livresDisponibles) > 1 ? 's' : '' ?> disponible<?= count($livresDisponibles) > 1 ? 's' : '' ?></p>
                    <p><span class="stats-icon">👥</span><strong><?= count($utilisateurs) ?></strong> utilisateur<?= count($utilisateurs) > 1 ? 's' : '' ?> actif<?= count($utilisateurs) > 1 ? 's' : '' ?></p>
                    <p><span class="stats-icon">📋</span><strong><?= count($empruntsEnCours) ?></strong> emprunt<?= count($empruntsEnCours) > 1 ? 's' : '' ?> en cours</p>
                    <?php if (count($empruntsEnRetard) > 0): ?>
                        <p style="color: #e53e3e;"><span class="stats-icon">⚠️</span><strong><?= count($empruntsEnRetard) ?></strong> emprunt<?= count($empruntsEnRetard) > 1 ? 's' : '' ?> en retard</p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card">
                <h3>🚀 Actions rapides</h3>
                <div class="action-buttons">
                    <a href="livres/ajouter.php" class="btn btn-success">📖 Ajouter un livre</a>
                    <a href="emprunts/ajouter.php" class="btn btn-success">📋 Nouvel emprunt</a>
                    <a href="utilisateurs/ajouter.php" class="btn btn-success">� Nouvel utilisateur</a>
                    <a href="ecrivains/ajouter.php" class="btn btn-success">✍️ Nouvel écrivain</a>
                    <a href="genres/ajouter.php" class="btn btn-success">🏷️ Nouveau genre</a>
                    <a href="emprunts/index.php" class="btn btn-warning">⚡ Gérer les emprunts</a>
                </div>
            </div>

            <div class="card">
                <h3>📖 Derniers livres ajoutés</h3>
                <?php if (count($livres) > 0): ?>
                    <?php 
                    $derniersLivres = array_slice($livres, -5);
                    foreach ($derniersLivres as $livre): 
                    ?>
                        <div class="book-item">
                            📚 <?= htmlspecialchars($livre['titre']) ?> - <?= htmlspecialchars($livre['auteur']) ?>
                        </div>
                    <?php endforeach; ?>
                    <br>
                    <a href="livres/index.php" class="btn">📚 Voir tous les livres</a>
                <?php else: ?>
                    <p style="color: #666; font-style: italic; text-align: center; padding: 20px;">
                        📝 Aucun livre enregistré pour le moment
                    </p>
                    <div style="text-align: center;">
                        <a href="livres/ajouter.php" class="btn btn-success">➕ Ajouter le premier livre</a>
                    </div>
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
        
        <?php endif; ?>
        
    </div>
</body>
</html>
