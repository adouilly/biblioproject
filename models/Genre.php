<?php
require_once __DIR__ . '/../config/database.php';

class Genre {
    private $pdo;
    private $table = 'genres';

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    // Lister tous les genres
    public function listerTous() {
        $sql = "SELECT g.*, 
                       COUNT(l.id_livre) as nb_livres 
                FROM {$this->table} g 
                LEFT JOIN livres l ON g.id_genre = l.id_genre 
                GROUP BY g.id_genre, g.nom_genre, g.description 
                ORDER BY g.nom_genre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtenir un genre par ID
    public function obtenirParId($id) {
        $sql = "SELECT * FROM {$this->table} WHERE id_genre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Ajouter un genre
    public function ajouter($nom_genre, $description = '') {
        $sql = "INSERT INTO {$this->table} (nom_genre, description) VALUES (?, ?)";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom_genre, $description]);
    }

    // Modifier un genre
    public function modifier($id, $nom_genre, $description = '') {
        $sql = "UPDATE {$this->table} SET nom_genre = ?, description = ? WHERE id_genre = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$nom_genre, $description, $id]);
    }

    // Supprimer un genre
    public function supprimer($id) {
        // Vérifier s'il y a des livres associés
        $sql_check = "SELECT COUNT(*) FROM livres WHERE id_genre = ?";
        $stmt_check = $this->pdo->prepare($sql_check);
        $stmt_check->execute([$id]);
        
        if ($stmt_check->fetchColumn() > 0) {
            throw new Exception("Impossible de supprimer : ce genre a des livres associés");
        }
        
        $sql = "DELETE FROM {$this->table} WHERE id_genre = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$id]);
    }

    // Rechercher des genres
    public function rechercher($terme) {
        $sql = "SELECT g.*, 
                       COUNT(l.id_livre) as nb_livres 
                FROM {$this->table} g 
                LEFT JOIN livres l ON g.id_genre = l.id_genre 
                WHERE g.nom_genre LIKE ? OR g.description LIKE ?
                GROUP BY g.id_genre, g.nom_genre, g.description 
                ORDER BY g.nom_genre";
        $terme = "%{$terme}%";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$terme, $terme]);
        return $stmt->fetchAll();
    }

    // Obtenir les livres d'un genre
    public function obtenirLivres($id) {
        $sql = "SELECT * FROM v_livres_complets WHERE id_genre = ? ORDER BY titre";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetchAll();
    }
}
?>
