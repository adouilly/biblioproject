<?php
require_once __DIR__ . '/../config/database.php';

class Livre {
    private $pdo;
    private $table = 'livres';

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    // Lister tous les livres avec auteur et genre
    public function listerTous() {
        $sql = "SELECT * FROM v_livres_complets ORDER BY titre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lister les livres disponibles
    public function listerDisponibles() {
        $sql = "SELECT * FROM v_livres_complets WHERE disponible = 1 ORDER BY titre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtenir un livre par ID
    public function obtenirParId($id) {
        $sql = "SELECT * FROM v_livres_complets WHERE id_livre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Ajouter un nouveau livre
    public function ajouter($titre, $annee_publication, $isbn, $id_ecrivain, $id_genre) {
        $sql = "INSERT INTO {$this->table} (titre, annee_publication, isbn, id_ecrivain, id_genre) 
                VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titre, $annee_publication, $isbn, $id_ecrivain, $id_genre]);
    }

    // Modifier un livre
    public function modifier($id, $titre, $annee_publication, $isbn, $id_ecrivain, $id_genre) {
        $sql = "UPDATE {$this->table} 
                SET titre = ?, annee_publication = ?, isbn = ?, id_ecrivain = ?, id_genre = ? 
                WHERE id_livre = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$titre, $annee_publication, $isbn, $id_ecrivain, $id_genre, $id]);
    }

    // Supprimer un livre
    public function supprimer($id) {
        // Vérifier d'abord s'il y a des emprunts en cours
        $sql_check = "SELECT COUNT(*) FROM emprunts WHERE id_livre = ? AND statut = 'en_cours'";
        $stmt_check = $this->pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        
        if ($stmt_check->fetchColumn() > 0) {
            throw new Exception("Impossible de supprimer : le livre est actuellement emprunté");
        }
        
        $sql = "DELETE FROM {$this->table} WHERE id_livre = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Vérifier la disponibilité d'un livre
    public function estDisponible($id) {
        $sql = "SELECT disponible FROM {$this->table} WHERE id_livre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        $result = $stmt->fetch();
        return $result ? (bool)$result['disponible'] : false;
    }

    // Rechercher des livres
    public function rechercher($terme) {
        $sql = "SELECT * FROM v_livres_complets 
                WHERE titre LIKE ? OR auteur LIKE ? OR nom_genre LIKE ?
                ORDER BY titre";
        $terme = "%{$terme}%";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$terme, $terme, $terme]);
        return $stmt->fetchAll();
    }

    // Obtenir le dernier ID inséré
    public function getDernierIdInsere() {
        return $this->pdo->lastInsertId();
    }
}
?>
