-- Vue pour les emprunts en cours avec statut détaillé
CREATE OR REPLACE VIEW v_emprunts_en_cours AS
SELECT 
    emp.id_emprunt,
    emp.date_emprunt,
    emp.date_retour_prevue,
    CASE 
        WHEN emp.statut = 'en_cours' AND CURDATE() > emp.date_retour_prevue THEN DATEDIFF(CURDATE(), emp.date_retour_prevue)
        WHEN emp.statut = 'en_cours' THEN DATEDIFF(emp.date_retour_prevue, CURDATE())
        ELSE DATEDIFF(emp.date_retour_effective, emp.date_emprunt)
    END AS jours_restants,
    CASE 
        WHEN emp.statut = 'en_cours' AND CURDATE() > emp.date_retour_prevue THEN 'EN RETARD'
        WHEN emp.statut = 'en_cours' AND DATEDIFF(emp.date_retour_prevue, CURDATE()) <= 3 THEN 'BIENTÔT DÛ'
        WHEN emp.statut = 'en_cours' THEN 'EN COURS'
        ELSE 'RENDU'
    END AS statut_detail,
    l.titre,
    CONCAT(e.prenom, ' ', e.nom) AS auteur,
    CONCAT(u.prenom, ' ', u.nom) AS emprunteur,
    u.email,
    emp.statut,
    emp.date_retour_effective,
    emp.remarques,
    emp.id_livre,
    emp.id_utilisateur
FROM emprunts emp
JOIN livres l ON emp.id_livre = l.id_livre
JOIN ecrivains e ON l.id_ecrivain = e.id_ecrivain
JOIN utilisateurs u ON emp.id_utilisateur = u.id_utilisateur
WHERE emp.statut = 'en_cours';
