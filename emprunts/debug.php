<?php
// Script de débogage pour vérifier les emprunts
require_once '../config/database.php';
require_once '../models/Emprunt.php';

$empruntModel = new Emprunt();

echo "<h2>🔍 Débogage des emprunts</h2>\n";

// Test 1: Compter tous les emprunts
try {
    $database = new Database();
    $pdo = $database->getConnection();
    
    $sql = "SELECT COUNT(*) as total FROM emprunts";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $total = $stmt->fetch();
    echo "<p><strong>Total emprunts en base :</strong> " . $total['total'] . "</p>\n";
    
    // Détail par statut
    $sql = "SELECT statut, COUNT(*) as count FROM emprunts GROUP BY statut";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $stats = $stmt->fetchAll();
    
    echo "<p><strong>Répartition par statut :</strong></p>\n";
    echo "<ul>\n";
    foreach ($stats as $stat) {
        echo "<li>" . $stat['statut'] . " : " . $stat['count'] . "</li>\n";
    }
    echo "</ul>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>\n";
}

// Test 2: Lister les emprunts récents
echo "<h3>📚 Emprunts récents (5 derniers)</h3>\n";
try {
    $sql = "SELECT 
                emp.id_emprunt,
                emp.date_emprunt,
                emp.statut,
                l.titre,
                CONCAT(u.prenom, ' ', u.nom) AS emprunteur
            FROM emprunts emp
            JOIN livres l ON emp.id_livre = l.id_livre
            JOIN utilisateurs u ON emp.id_utilisateur = u.id_utilisateur
            ORDER BY emp.date_emprunt DESC
            LIMIT 5";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $emprunts = $stmt->fetchAll();
    
    echo "<table border='1' cellpadding='5'>\n";
    echo "<tr><th>ID</th><th>Date</th><th>Statut</th><th>Livre</th><th>Emprunteur</th></tr>\n";
    foreach ($emprunts as $emprunt) {
        echo "<tr>";
        echo "<td>" . $emprunt['id_emprunt'] . "</td>";
        echo "<td>" . $emprunt['date_emprunt'] . "</td>";
        echo "<td>" . $emprunt['statut'] . "</td>";
        echo "<td>" . htmlspecialchars($emprunt['titre']) . "</td>";
        echo "<td>" . htmlspecialchars($emprunt['emprunteur']) . "</td>";
        echo "</tr>\n";
    }
    echo "</table>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur : " . $e->getMessage() . "</p>\n";
}

// Test 3: Test des méthodes du modèle
echo "<h3>🧪 Test des méthodes du modèle</h3>\n";
try {
    $empruntsEnCours = $empruntModel->listerEnCours();
    echo "<p><strong>Emprunts en cours (méthode) :</strong> " . count($empruntsEnCours) . "</p>\n";
    
    $tousEmprunts = $empruntModel->listerTous();
    echo "<p><strong>Tous les emprunts (méthode) :</strong> " . count($tousEmprunts) . "</p>\n";
    
    $retoursRecents = $empruntModel->listerRetoursRecents();
    echo "<p><strong>Retours récents (méthode) :</strong> " . count($retoursRecents) . "</p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>Erreur méthode : " . $e->getMessage() . "</p>\n";
}

echo "<p><a href='index.php'>← Retour aux emprunts</a></p>\n";
?>
