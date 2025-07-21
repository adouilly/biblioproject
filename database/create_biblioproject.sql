-- ========================================
-- CRÉATION BASE DE DONNÉES BIBLIOTHÈQUE
-- Version fonctionnelle simplifiée
-- ========================================

-- Suppression et création de la base
DROP DATABASE IF EXISTS biblioproject;
CREATE DATABASE biblioproject;
USE biblioproject;

-- ========================================
-- CRÉATION DES TABLES
-- ========================================

-- Table des écrivains
CREATE TABLE ecrivains (
    id_ecrivain INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    nationalite VARCHAR(100) DEFAULT NULL,
    date_naissance DATE DEFAULT NULL
);

-- Table des genres
CREATE TABLE genres (
    id_genre INT AUTO_INCREMENT PRIMARY KEY,
    nom_genre VARCHAR(50) NOT NULL UNIQUE,
    description TEXT DEFAULT NULL
);

-- Table des utilisateurs
CREATE TABLE utilisateurs (
    id_utilisateur INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    actif TINYINT(1) DEFAULT 1
);

-- Table des livres
CREATE TABLE livres (
    id_livre INT AUTO_INCREMENT PRIMARY KEY,
    titre VARCHAR(200) NOT NULL,
    annee_publication INT DEFAULT NULL,
    isbn VARCHAR(20) DEFAULT NULL UNIQUE,
    disponible TINYINT(1) DEFAULT 1,
    id_ecrivain INT NOT NULL,
    id_genre INT NOT NULL,
    FOREIGN KEY (id_ecrivain) REFERENCES ecrivains(id_ecrivain),
    FOREIGN KEY (id_genre) REFERENCES genres(id_genre)
);

-- Table des emprunts
CREATE TABLE emprunts (
    id_emprunt INT AUTO_INCREMENT PRIMARY KEY,
    date_emprunt DATE NOT NULL,
    date_retour_prevue DATE NOT NULL,
    date_retour_effective DATE DEFAULT NULL,
    statut VARCHAR(20) DEFAULT 'en_cours',
    remarques TEXT DEFAULT NULL,
    id_livre INT NOT NULL,
    id_utilisateur INT NOT NULL,
    FOREIGN KEY (id_livre) REFERENCES livres(id_livre),
    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id_utilisateur)
);

-- ========================================
-- INSERTION DES DONNÉES DE TEST
-- ========================================

-- Genres littéraires
INSERT INTO genres (nom_genre, description) VALUES
('Roman', 'Œuvre littéraire narrative'),
('Science-Fiction', 'Fiction basée sur les sciences et technologies'),
('Fantastique', 'Genre mêlant réel et surnaturel'),
('Thriller', 'Genre de fiction créant un sentiment de suspense'),
('Biographie', 'Récit de la vie d une personne'),
('Essai', 'Ouvrage de réflexion'),
('Poésie', 'Expression littéraire en vers'),
('Histoire', 'Récit d événements passés');

-- Écrivains
INSERT INTO ecrivains (nom, prenom, nationalite, date_naissance) VALUES
('Hugo', 'Victor', 'Française', '1802-02-26'),
('Tolkien', 'J.R.R.', 'Britannique', '1892-01-03'),
('Asimov', 'Isaac', 'Américaine', '1920-01-02'),
('Christie', 'Agatha', 'Britannique', '1890-09-15'),
('Camus', 'Albert', 'Française', '1913-11-07'),
('Orwell', 'George', 'Britannique', '1903-06-25'),
('Rowling', 'J.K.', 'Britannique', '1965-07-31'),
('Dumas', 'Alexandre', 'Française', '1802-07-24');

-- Utilisateurs
INSERT INTO utilisateurs (nom, prenom, email) VALUES
('Martin', 'Jean', 'jean.martin@email.com'),
('Dubois', 'Marie', 'marie.dubois@email.com'),
('Petit', 'Pierre', 'pierre.petit@email.com'),
('Moreau', 'Sophie', 'sophie.moreau@email.com'),
('Laurent', 'Paul', 'paul.laurent@email.com');

-- Livres
INSERT INTO livres (titre, annee_publication, isbn, id_ecrivain, id_genre) VALUES
('Les Misérables', 1862, '978-2-07-040570-1', 1, 1),
('Le Seigneur des Anneaux', 1954, '978-2-07-061276-6', 2, 3),
('Fondation', 1951, '978-2-07-029284-4', 3, 2),
('Le Crime de l Orient-Express', 1934, '978-2-253-00661-8', 4, 4),
('L Étranger', 1942, '978-2-07-036002-5', 5, 1),
('1984', 1949, '978-2-07-036822-9', 6, 2),
('Harry Potter à l école des sorciers', 1997, '978-2-07-054120-7', 7, 3),
('Le Comte de Monte-Cristo', 1844, '978-2-07-040445-2', 8, 1);

-- Emprunts de test
INSERT INTO emprunts (id_livre, id_utilisateur, date_emprunt, date_retour_prevue) VALUES
(1, 1, '2025-07-01', '2025-07-16'),
(3, 2, '2025-07-10', '2025-07-25'),
(5, 3, '2025-07-15', '2025-07-30');

-- Mise à jour de la disponibilité des livres empruntés
UPDATE livres SET disponible = 0 WHERE id_livre IN (1, 3, 5);

-- ========================================
-- CRÉATION DES VUES UTILES
-- ========================================

-- Vue des livres avec toutes les informations
CREATE VIEW v_livres_complets AS
SELECT 
    l.id_livre,
    l.titre,
    l.annee_publication,
    l.isbn,
    l.disponible,
    CONCAT(e.prenom, ' ', e.nom) AS auteur,
    e.nationalite,
    g.nom_genre,
    l.id_ecrivain,
    l.id_genre
FROM livres l
JOIN ecrivains e ON l.id_ecrivain = e.id_ecrivain
JOIN genres g ON l.id_genre = g.id_genre;

-- Vue des emprunts en cours avec détails
CREATE VIEW v_emprunts_en_cours AS
SELECT 
    emp.id_emprunt,
    emp.date_emprunt,
    emp.date_retour_prevue,
    DATEDIFF(emp.date_retour_prevue, CURDATE()) AS jours_restants,
    CASE 
        WHEN CURDATE() > emp.date_retour_prevue THEN 'EN RETARD'
        WHEN DATEDIFF(emp.date_retour_prevue, CURDATE()) <= 3 THEN 'BIENTÔT DÛ'
        ELSE 'EN COURS'
    END AS statut_detail,
    l.titre,
    CONCAT(e.prenom, ' ', e.nom) AS auteur,
    CONCAT(u.prenom, ' ', u.nom) AS emprunteur,
    u.email
FROM emprunts emp
JOIN livres l ON emp.id_livre = l.id_livre
JOIN ecrivains e ON l.id_ecrivain = e.id_ecrivain
JOIN utilisateurs u ON emp.id_utilisateur = u.id_utilisateur
WHERE emp.statut = 'en_cours';

-- ========================================
-- VÉRIFICATION DES DONNÉES
-- ========================================

SELECT 'VÉRIFICATION DE LA CRÉATION' AS message;
SELECT COUNT(*) AS nb_genres FROM genres;
SELECT COUNT(*) AS nb_ecrivains FROM ecrivains;
SELECT COUNT(*) AS nb_utilisateurs FROM utilisateurs;
SELECT COUNT(*) AS nb_livres FROM livres;
SELECT COUNT(*) AS nb_emprunts FROM emprunts;

SELECT 'LIVRES AVEC AUTEURS ET GENRES' AS message;
SELECT 
    l.titre,
    CONCAT(e.prenom, ' ', e.nom) AS auteur,
    g.nom_genre,
    CASE WHEN l.disponible = 1 THEN 'Disponible' ELSE 'Emprunté' END AS statut
FROM livres l
JOIN ecrivains e ON l.id_ecrivain = e.id_ecrivain
JOIN genres g ON l.id_genre = g.id_genre
ORDER BY l.titre;

SELECT 'EMPRUNTS EN COURS' AS message;
SELECT 
    CONCAT(u.prenom, ' ', u.nom) AS emprunteur,
    l.titre,
    emp.date_emprunt,
    emp.date_retour_prevue
FROM emprunts emp
JOIN livres l ON emp.id_livre = l.id_livre
JOIN utilisateurs u ON emp.id_utilisateur = u.id_utilisateur
WHERE emp.statut = 'en_cours'
ORDER BY emp.date_emprunt;
