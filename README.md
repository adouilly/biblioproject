# Projet Bibliothèque - Système de Gestion

## Description
Application web de gestion de bibliothèque permettant de gérer les livres, écrivains, genres, utilisateurs et emprunts.

## Structure du projet

```
BiblioProject/
├── database/
│   ├── create_database.sql     # Script de création de la BDD
│   └── config.php             # Configuration de connexion
├── models/                    # Classes métier (PDO)
├── views/                     # Templates HTML
├── controllers/               # Logique métier
├── assets/                    # CSS, JS, images
├── includes/                  # Fichiers communs
├── index.php                  # Page d'accueil
├── MCD.md                     # Documentation du MCD
└── README.md                  # Ce fichier
```

## Fonctionnalités

### Livres
- [x] Lister tous les livres avec auteur et genre
- [ ] Ajouter un nouveau livre
- [ ] Modifier un livre
- [ ] Supprimer un livre
- [ ] Afficher la disponibilité

### Écrivains
- [ ] CRUD des écrivains (nom, prénom, nationalité)
- [ ] Associer un livre à un écrivain

### Genres
- [ ] CRUD des genres

### Utilisateurs
- [ ] CRUD des utilisateurs (id, nom, prénom, email)

### Emprunts
- [ ] Enregistrer un emprunt
- [ ] Marquer un emprunt comme rendu
- [ ] Historique des emprunts d'un utilisateur
- [ ] Empêcher l'emprunt d'un livre déjà emprunté

## Base de données

### Tables principales :
- **ecrivains** : Auteurs des livres
- **genres** : Catégories littéraires
- **utilisateurs** : Personnes qui empruntent
- **livres** : Catalogue des ouvrages
- **emprunts** : Transactions d'emprunt

### Relations :
- Un livre a un écrivain et un genre
- Un emprunt lie un livre à un utilisateur
- Contraintes d'intégrité et triggers automatiques

## Installation

1. Créer la base de données MySQL
2. Exécuter le script `database/create_database.sql`
3. Configurer les paramètres dans `database/config.php`
4. Lancer un serveur web (Apache/Nginx) ou PHP built-in server

## Technologies utilisées
- **Backend** : PHP 8+ avec PDO
- **Base de données** : MySQL 8+
- **Frontend** : HTML5, CSS3, JavaScript vanilla
- **Sécurité** : Requêtes préparées PDO

## Contraintes respectées
- Utilisation exclusive de PDO
- Requêtes préparées pour la sécurité
- Structure MVC basique
- Validation des données
- Gestion des erreurs
"# biblioproject" 
