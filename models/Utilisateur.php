<?php
require_once __DIR__ . '/../config/database.php';

class Utilisateur {
    private $pdo;
    private $table = 'utilisateurs';

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    // Lister tous les utilisateurs
    public function listerTous() {
        $sql = "SELECT * FROM {$this->table} ORDER BY nom, prenom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lister les utilisateurs actifs
    public function listerActifs() {
        $sql = "SELECT * FROM {$this->table} WHERE actif = 1 ORDER BY nom, prenom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtenir un utilisateur par ID
    public function obtenirParId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_utilisateur = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Ajouter un utilisateur
    public function ajouter($nom, $prenom, $email) {
        // Validation email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format d'email invalide");
        }
        
        $sql = "INSERT INTO {$this->table} (nom, prenom, email) VALUES (?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom, $prenom, $email]);
    }

    // Modifier un utilisateur
    public function modifier($id, $nom, $prenom, $email, $actif = true) {
        // Validation email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Format d'email invalide");
        }
        
        $sql = "UPDATE {$this->table} SET nom = ?, prenom = ?, email = ?, actif = ? WHERE id_utilisateur = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom, $prenom, $email, $actif, $id]);
    }

    // Désactiver un utilisateur (au lieu de supprimer)
    public function desactiver($id) {
        $sql = "UPDATE {$this->table} SET actif = 0 WHERE id_utilisateur = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Supprimer un utilisateur
    public function supprimer($id) {
        // Vérifier s'il y a des emprunts en cours
        $sql_check = "SELECT COUNT(*) FROM emprunts WHERE id_utilisateur = ? AND statut = 'en_cours'";
        $stmt_check = $this->pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        
        if ($stmt_check->fetchColumn() > 0) {
            throw new Exception("Impossible de supprimer : cet utilisateur a des emprunts en cours");
        }
        
        $sql = "DELETE FROM {$this->table} WHERE id_utilisateur = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Rechercher des utilisateurs
    public function rechercher($terme) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nom LIKE ? OR prenom LIKE ? OR email LIKE ?
                ORDER BY nom, prenom";
        $terme = "%{$terme}%";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$terme, $terme, $terme]);
        return $stmt->fetchAll();
    }

    // Vérifier si un email existe déjà
    public function emailExiste($email, $id_exclure = null) {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($id_exclure) {
            $sql .= " AND id_utilisateur != ?";
            $params[] = $id_exclure;
        }
        
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn() > 0;
    }

    // Obtenir l'historique des emprunts d'un utilisateur
    public function obtenirHistoriqueEmprunts($id) {
        $sql = "SELECT e.*, l.titre, CONCAT(ec.prenom, ' ', ec.nom) as auteur, g.nom_genre
                FROM emprunts e
                JOIN livres l ON e.id_livre = l.id_livre
                JOIN ecrivains ec ON l.id_ecrivain = ec.id_ecrivain
                JOIN genres g ON l.id_genre = g.id_genre
                WHERE e.id_utilisateur = ?
                ORDER BY e.date_emprunt DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }
}
?>
