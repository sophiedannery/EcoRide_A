INSERT INTO user (email, roles, password, pseudo, credits, statut, date_creation) 
VALUES
('admin@ecoride.com', '["ROLE_ADMIN"]', '$2y$13$wHeJOKFmaPzQfM0W9BUKh.DAPrF6//siPt.vcgKG0U/UYqFESOfiK', 'Administrateur', NULL, 'admin', '2025-01-10 09:00:00'),
('employe1@ecoride.com', '["ROLE_EMPLOYEE"]', '$2y$13$Rbr435okRfnXHXevl6OO0upRw.gXgtQYqzlOgTrH0ck3us0fD6qja', 'Employe1', NULL, 'employe', '2025-02-15 19:00:00'),
('employe2@ecoride.com', '["ROLE_EMPLOYEE"]', '$2y$13$4IZltolXtOdyK2XJYetVle9o/biOxdjyboLoROKkcDqUqlhsxoFwq', 'Employe2', NULL, 'employe', '2025-02-15 20:00:00'),
('maddie@ecoride.com', '["ROLE_USER"]', '$2y$13$WxIEnq7ee1q/fqop67qcg.SpXf9mwjnm7RoYSZM15P36RP8uiLe8a', 'Maddie', 20, 'passager_chauffeur', '2025-02-15 20:00:00'),
('raph@ecoride.com', '["ROLE_USER"]', '$2y$13$7.qfuF3yqV9CrMDkfMKdPOtl53HsbL5Ee4uXyPQ6ZTS/1zXbkkcG6', 'Raph', 100, 'passager_chauffeur', '2025-02-12 20:00:00'),
('sophie@ecoride.com', '["ROLE_USER"]', '$2y$13$r2W7PAquHneg5.8y8i3nQOEtSeWh3vUJBwlpsBMWi/eBZDdS8EzMW', 'Sophie', 80, 'passager_chauffeur', '2025-02-15 20:00:00'),
('laura@ecoride.com', '["ROLE_USER"]', '$2y$13$QzJOJmYCcviJrVo6wqWw6OYLtpWLPoyeLCJGxNgdvVB97bPE9fFm6', 'Laura', 300, 'passager_chauffeur', '2025-03-25 20:00:00'),
('cle@ecoride.com', '["ROLE_USER"]', '$2y$13$7Z.gl8.WiPW2ox1jj6kx7.15fg0/TppyrLkahRpUnUlDaMuqaLrAW', 'Cle', 180, 'passager_chauffeur', '2025-04-30 20:00:00'),
('vin@ecoride.com', '["ROLE_USER"]', '$2y$13$lXob9hJYex79F2UKwupH0.PDzP2SdPMbgSdr5hwD7rzglScqFKBkS', 'Vin', 120, 'passager_chauffeur', '2025-05-02 20:00:00'),
('luca@ecoride.com', '["ROLE_USER"]', '$2y$13$HwHY2EtAym07LWofVuThyuZx6UTy3C6Wbv4ExxrvQT5unlkAaIwFW', 'Luca', 50, 'passager_chauffeur', '2025-05-02 20:00:00');

INSERT INTO preference (libelle) 
VALUES
('Fumeur'),
('Non-fumeur'),
('Avec animaux'),
('Sans animaux');

INSERT INTO preference_user (preference_id, user_id)
VALUES
(1, 5),
(3, 5),
(2, 4),
(1, 6),
(3, 8),
(1, 8),
(2, 9),
(1, 10);

INSERT INTO vehicule (user_id, immatriculation, date_premiere_immatriculation, marque, modele, couleur, places_disponibles, energie)
VALUES
(5, 'AB-123-CD', '2020-06-15', 'Renault', 'Clio', 'Rouge', 4, 'essence'),
(7, 'FE-436-GH', '2023-03-12', 'Peugeot', '208', 'Bleue', 2, 'diesel'),
(9, 'GF-357-JH', '2021-03-12', 'Citroën', 'C4', 'Grise', 3, 'electrique'),
(9, 'FG-345-GH', '2024-07-23', 'Tesla', 'Mobile', 'Noire', 3, 'electrique');

INSERT INTO trajet (chauffeur_id, vehicule_id, adresse_depart, adresse_arrivee, date_depart, date_arrivee, prix, places_restantes, statut, energie)
VALUES
(5, 1, 'Paris', 'Lyon', '2025-07-01 10:00:00', '2025-07-01 17:00:00', 30, 3, 'confirmé', 'essence'),
(7, 2, 'Marseille', 'Buis', '2025-07-02 10:00:00', '2025-07-02 13:00:00', 22, 2, 'confirmé', 'diesel'),
(9, 3, 'Bordeaux', 'Toulouse', '2025-07-03 09:00:00', '2025-07-03 11:00:00', 22, 2, 'confirmé', 'electrique');

INSERT INTO reservation (trajet_id, passager_id, date_confirmation, statut, credits_utilises)
VALUES
(1, 10, '2025-06-03 09:00:00', 'confirmée', 30),
(1, 9, '2025-06-03 09:00:00', 'confirmée', 30),
(2, 5, '2025-06-03 09:00:00', 'confirmée', 22); 

INSERT INTO `transaction` (user_id, trajet_id, montant, date_transaction, type)
VALUES
(10, 1, 30, '2025-06-04 09:00:00', 'paiement'),
(9, 1, 30, '2025-06-04 09:05:00', 'paiement'),
(5, 2, 22, '2025-06-04 09:10:00', 'paiement');

INSERT INTO avis (reservation_id, employe_valideur_id, note, commentaire, date_creation, statut_validation)
VALUES
(1, 3, 5, 'Super trajet', '2025-06-05 09:00:00', 'validé');