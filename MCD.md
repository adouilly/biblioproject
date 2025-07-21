# MCD - Système de Gestion de Bibliothèque

## Entités identifiées :

### 1. ÉCRIVAINS
- **id_ecrivain** (PK) : INT AUTO_INCREMENT
- nom : VARCHAR(100) NOT NULL
- prenom : VARCHAR(100) NOT NULL
- nationalite : VARCHAR(100)

### 2. GENRES
- **id_genre** (PK) : INT AUTO_INCREMENT
- nom_genre : VARCHAR(50) NOT NULL UNIQUE

### 3. LIVRES
- **id_livre** (PK) : INT AUTO_INCREMENT
- titre : VARCHAR(200) NOT NULL
- annee_publication : INT
- isbn : VARCHAR(20) UNIQUE
- disponible : BOOLEAN DEFAULT TRUE
- **id_ecrivain** (FK) : INT NOT NULL
- **id_genre** (FK) : INT NOT NULL

### 4. UTILISATEURS
- **id_utilisateur** (PK) : INT AUTO_INCREMENT
- nom : VARCHAR(100) NOT NULL
- prenom : VARCHAR(100) NOT NULL
- email : VARCHAR(150) NOT NULL UNIQUE

### 5. EMPRUNTS
- **id_emprunt** (PK) : INT AUTO_INCREMENT
- date_emprunt : DATE NOT NULL
- date_retour_prevue : DATE NOT NULL
- date_retour_effective : DATE NULL
- statut : ENUM('en_cours', 'rendu') DEFAULT 'en_cours'
- **id_livre** (FK) : INT NOT NULL
- **id_utilisateur** (FK) : INT NOT NULL

## Relations :

1. **ÉCRIVAINS ↔ LIVRES** : Un écrivain peut écrire plusieurs livres, un livre a un seul écrivain (1,n)
2. **GENRES ↔ LIVRES** : Un genre peut concerner plusieurs livres, un livre a un seul genre (1,n)
3. **LIVRES ↔ EMPRUNTS** : Un livre peut être emprunté plusieurs fois, un emprunt concerne un seul livre (1,n)
4. **UTILISATEURS ↔ EMPRUNTS** : Un utilisateur peut faire plusieurs emprunts, un emprunt concerne un seul utilisateur (1,n)

## Contraintes métier :

- Un livre ne peut être emprunté que s'il est disponible
- La durée d'emprunt par défaut est de 15 jours
- Un utilisateur ne peut emprunter le même livre qu'une seule fois à la fois
- L'email des utilisateurs doit être unique
- Le statut disponible du livre dépend de l'existence d'emprunts en cours

## Diagramme MCD (représentation textuelle) :

```
ÉCRIVAINS (id_ecrivain, nom, prenom, nationalite)
    |
    | 1,n (écrire)
    |
LIVRES (id_livre, titre, annee_publication, isbn, disponible, #id_ecrivain, #id_genre)
    |                                                               |
    | 1,n (être emprunté)                                          | n,1 (appartenir)
    |                                                               |
EMPRUNTS (id_emprunt, date_emprunt, date_retour_prevue,           GENRES (id_genre, nom_genre)
         date_retour_effective, statut, #id_livre, #id_utilisateur)
    |
    | n,1 (effectuer)
    |
UTILISATEURS (id_utilisateur, nom, prenom, email)
```

## Règles de gestion :

1. RG1 : Un livre est écrit par un seul écrivain
2. RG2 : Un livre appartient à un seul genre
3. RG3 : Un livre peut être emprunté plusieurs fois (mais pas en même temps)
4. RG4 : Un utilisateur peut emprunter plusieurs livres
5. RG5 : Un emprunt concerne un seul livre et un seul utilisateur
6. RG6 : Un livre emprunté n'est plus disponible jusqu'à son retour
7. RG7 : La date de retour prévue est calculée automatiquement (date_emprunt + 15 jours)
