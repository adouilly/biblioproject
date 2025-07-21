<?php
require_once __DIR__ . '/../config/database.php';

class Ecrivain {
    private $pdo;
    private $table = 'ecrivains';

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    // Lister tous les écrivains
    public function listerTous() {
        $sql = "SELECT * FROM {$this->table} ORDER BY nom, prenom";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtenir un écrivain par ID
    public function obtenirParId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_ecrivain = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Ajouter un écrivain
    public function ajouter($nom, $prenom, $nationalite, $date_naissance = null) {
        $sql = "INSERT INTO {$this->table} (nom, prenom, nationalite, date_naissance) VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom, $prenom, $nationalite, $date_naissance]);
    }

    // Modifier un écrivain
    public function modifier($id, $nom, $prenom, $nationalite, $date_naissance = null) {
        $sql = "UPDATE {$this->table} SET nom = ?, prenom = ?, nationalite = ?, date_naissance = ? WHERE id_ecrivain = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom, $prenom, $nationalite, $date_naissance, $id]);
    }

    // Supprimer un écrivain
    public function supprimer($id) {
        // Vérifier s'il y a des livres associés
        $sql_check = "SELECT COUNT(*) FROM livres WHERE id_ecrivain = ?";
        $stmt_check = $this->pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        
        if ($stmt_check->fetchColumn() > 0) {
            throw new Exception("Impossible de supprimer : cet écrivain a des livres associés");
        }
        
        $sql = "DELETE FROM {$this->table} WHERE id_ecrivain = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Rechercher des écrivains
    public function rechercher($terme) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE nom LIKE ? OR prenom LIKE ? OR nationalite LIKE ?
                ORDER BY nom, prenom";
        $terme = "%{$terme}%";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$terme, $terme, $terme]);
        return $stmt->fetchAll();
    }

    // Obtenir les livres d'un écrivain
    public function obtenirLivres($id) {
        $sql = "SELECT * FROM v_livres_complets WHERE id_ecrivain = ? ORDER BY titre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }
}
?>
