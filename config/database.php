<?php
class Database {
    private $host = 'localhost';
    private $db_name = 'biblioproject';
    private $username = 'root'; // Changez selon votre configuration
    private $password = '';     // Changez selon votre configuration
    private $pdo;

    public function getConnection() {
        $this->pdo = null;
        
        try {
            $dsn = "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4";
            $this->pdo = new PDO($dsn, $this->username, $this->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $this->pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        } catch(PDOException $e) {
            echo "Erreur de connexion : " . $e->getMessage();
        }
        
        return $this->pdo;
    }
}
?>
