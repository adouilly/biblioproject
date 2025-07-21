<?php
require_once __DIR__ . '/../config/database.php';

class Emprunt {
    private $pdo;
    private $table = 'emprunts';

    public function __construct() {
        $database = new Database();
        $this->pdo = $database->getConnection();
    }

    // Lister tous les emprunts
    public function listerTous() {
        $sql = "SELECT * FROM v_emprunts_en_cours 
                UNION ALL
                SELECT 
                    emp.id_emprunt,
                    emp.date_emprunt,
                    emp.date_retour_prevue,
                    DATEDIFF(emp.date_retour_effective, emp.date_emprunt) AS jours_restants,
                    'RENDU' AS statut_detail,
                    l.titre,
                    CONCAT(e.prenom, ' ', e.nom) AS auteur,
                    CONCAT(u.prenom, ' ', u.nom) AS emprunteur,
                    u.email
                FROM emprunts emp
                JOIN livres l ON emp.id_livre = l.id_livre
                JOIN ecrivains e ON l.id_ecrivain = e.id_ecrivain
                JOIN utilisateurs u ON emp.id_utilisateur = u.id_utilisateur
                WHERE emp.statut = 'rendu'
                ORDER BY date_emprunt DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lister les emprunts en cours
    public function listerEnCours() {
        $sql = "SELECT * FROM v_emprunts_en_cours ORDER BY date_emprunt DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Enregistrer un emprunt
    public function enregistrer($id_livre, $id_utilisateur, $date_emprunt = null) {
        // Vérifier si le livre est disponible
        if (!$this->livreEstDisponible($id_livre)) {
            throw new Exception("Ce livre n'est pas disponible pour l'emprunt");
        }
        
        // Date d'emprunt par défaut = aujourd'hui
        if (!$date_emprunt) {
            $date_emprunt = date('Y-m-d');
        }
        
        // Date de retour prévue = 15 jours après l'emprunt
        $date_retour_prevue = date('Y-m-d', strtotime($date_emprunt . ' + 15 days'));
        
        $sql = "INSERT INTO {$this->table} (id_livre, id_utilisateur, date_emprunt, date_retour_prevue) 
                VALUES (?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$id_livre, $id_utilisateur, $date_emprunt, $date_retour_prevue]);
        
        if ($result) {
            // Mettre à jour la disponibilité du livre
            $this->mettreAJourDisponibiliteLivre($id_livre, false);
        }
        
        return $result;
    }

    // Marquer un emprunt comme rendu
    public function marquerRendu($id_emprunt, $date_retour = null) {
        if (!$date_retour) {
            $date_retour = date('Y-m-d');
        }
        
        // Obtenir l'ID du livre pour mettre à jour sa disponibilité
        $sql_livre = "SELECT id_livre FROM {$this->table} WHERE id_emprunt = ?";
        $stmt_livre = $this->pdo->prepare($sql_livre);
        $stmt_livre->execute([$id_emprunt]);
        $livre = $stmt_livre->fetch();
        
        if (!$livre) {
            throw new Exception("Emprunt introuvable");
        }
        
        $sql = "UPDATE {$this->table} 
                SET statut = 'rendu', date_retour_effective = ? 
                WHERE id_emprunt = ?";
        $stmt = $this->pdo->prepare($sql);
        $result = $stmt->execute([$date_retour, $id_emprunt]);
        
        if ($result) {
            // Vérifier s'il n'y a plus d'autres emprunts en cours pour ce livre
            $sql_check = "SELECT COUNT(*) FROM {$this->table} 
                         WHERE id_livre = ? AND statut = 'en_cours' AND id_emprunt != ?";
            $stmt_check = $this->pdo->prepare($sql_check);
            $stmt_check->execute([$livre['id_livre'], $id_emprunt]);
            
            if ($stmt_check->fetchColumn() == 0) {
                // Mettre le livre comme disponible
                $this->mettreAJourDisponibiliteLivre($livre['id_livre'], true);
            }
        }
        
        return $result;
    }

    // Vérifier si un livre est disponible
    public function livreEstDisponible($id_livre) {
        $sql = "SELECT disponible FROM livres WHERE id_livre = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_livre]);
        $result = $stmt->fetch();
        return $result ? (bool)$result['disponible'] : false;
    }

    // Obtenir l'historique des emprunts d'un utilisateur
    public function obtenirHistoriqueUtilisateur($id_utilisateur) {
        $sql = "SELECT e.*, l.titre, CONCAT(ec.prenom, ' ', ec.nom) as auteur, g.nom_genre,
                CASE 
                    WHEN e.statut = 'en_cours' AND CURDATE() > e.date_retour_prevue THEN 'EN RETARD'
                    WHEN e.statut = 'en_cours' THEN 'EN COURS'
                    ELSE 'RENDU'
                END as statut_detail
                FROM {$this->table} e
                JOIN livres l ON e.id_livre = l.id_livre
                JOIN ecrivains ec ON l.id_ecrivain = ec.id_ecrivain
                JOIN genres g ON l.id_genre = g.id_genre
                WHERE e.id_utilisateur = ?
                ORDER BY e.date_emprunt DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_utilisateur]);
        return $stmt->fetchAll();
    }

    // Obtenir les emprunts en retard
    public function obtenirEnRetard() {
        $sql = "SELECT * FROM v_emprunts_en_cours 
                WHERE statut_detail = 'EN RETARD'
                ORDER BY date_retour_prevue";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Mettre à jour la disponibilité d'un livre
    private function mettreAJourDisponibiliteLivre($id_livre, $disponible) {
        $sql = "UPDATE livres SET disponible = ? WHERE id_livre = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([$disponible ? 1 : 0, $id_livre]);
    }

    // Obtenir un emprunt par ID
    public function obtenirParId($id) {
        $sql = "SELECT e.*, l.titre, CONCAT(ec.prenom, ' ', ec.nom) as auteur, 
                CONCAT(u.prenom, ' ', u.nom) as emprunteur, u.email
                FROM {$this->table} e
                JOIN livres l ON e.id_livre = l.id_livre
                JOIN ecrivains ec ON l.id_ecrivain = ec.id_ecrivain
                JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
                WHERE e.id_emprunt = ?";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    // Vérifier si un utilisateur a déjà emprunté un livre (et ne l'a pas encore rendu)
    public function utilisateurADejaPmprunte($id_utilisateur, $id_livre) {
        $sql = "SELECT COUNT(*) FROM {$this->table} 
                WHERE id_utilisateur = ? AND id_livre = ? AND statut = 'en_cours'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$id_utilisateur, $id_livre]);
        return $stmt->fetchColumn() > 0;
    }

    // Lister les emprunts en retard
    public function listerEnRetard() {
        $sql = "SELECT 
                    emp.id_emprunt,
                    emp.date_emprunt,
                    emp.date_retour_prevue,
                    DATEDIFF(CURDATE(), emp.date_retour_prevue) AS jours_retard,
                    l.titre,
                    CONCAT(e.prenom, ' ', e.nom) AS auteur,
                    CONCAT(u.prenom, ' ', u.nom) AS emprunteur,
                    u.email
                FROM {$this->table} emp
                JOIN livres l ON emp.id_livre = l.id_livre
                JOIN ecrivains e ON l.id_ecrivain = e.id_ecrivain
                JOIN utilisateurs u ON emp.id_utilisateur = u.id_utilisateur
                WHERE emp.statut = 'en_cours' AND emp.date_retour_prevue < CURDATE()
                ORDER BY emp.date_retour_prevue ASC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Obtenir les statistiques des emprunts
    public function obtenirStatistiques() {
        $stats = [];
        
        // Emprunts en cours
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE statut = 'en_cours'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['en_cours'] = $stmt->fetchColumn();
        
        // Emprunts en retard
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE statut = 'en_cours' AND date_retour_prevue < CURDATE()";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['en_retard'] = $stmt->fetchColumn();
        
        // Emprunts terminés
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE statut = 'rendu'";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['termines'] = $stmt->fetchColumn();
        
        // Total
        $sql = "SELECT COUNT(*) FROM {$this->table}";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute();
        $stats['total'] = $stmt->fetchColumn();
        
        return $stats;
    }
}
?>
