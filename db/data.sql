DELETE FROM transaction;
DELETE FROM suspension;
DELETE FROM avis;
DELETE FROM reservation;
DELETE FROM trajet;
DELETE FROM vehicule;
DELETE FROM user;



INSERT INTO user (id, email, roles, password, pseudo, credits, statut, date_creation)
VALUES
(1, 'admin@mail.com', '["ROLE_ADMIN"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Admin', NULL, 'Admin', '2025-07-18 12:32:40'),

(2, 'employe1@mail.com', '["ROLE_EMPLOYEE"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Admin', NULL, 'Employe1', '2025-07-18 12:32:40'),
(3, 'employe2@mail.com', '["ROLE_EMPLOYEE"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Admin', NULL, 'Employe2', '2025-07-18 12:32:40'),
(4, 'employe3@mail.com', '["ROLE_EMPLOYEE"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Admin', NULL, 'Employe3', '2025-07-18 12:32:40'),

(5, 'laura@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Laura', 30, 'chauffeur', '2025-07-18 12:32:40'),
(6, 'luca@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Luca', 40, 'chauffeur', '2025-07-18 12:32:40'),
(7, 'victoria@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Victoria', 25, 'chauffeur', '2025-07-18 12:32:40'),
(8, 'nine@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Nine', 10, 'chauffeur', '2025-07-18 12:32:40'),
(9, 'raphael@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Raphael', 15, 'chauffeur', '2025-07-18 12:32:40'),
(10, 'sophie@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Sophie', 18, 'chauffeur', '2025-07-18 12:32:40'),
(11, 'maddie@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Maddie', 22, 'chauffeur', '2025-07-18 12:32:40'),

(12, 'vinciance@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Vinciance', 35, 'passager', '2025-07-18 12:32:40'),
(13, 'clementine@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Clémentine', 28, 'passager', '2025-07-18 12:32:40'),
(14, 'chloe@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Chloé', 20, 'passager', '2025-07-18 12:32:40'),
(15, 'francois@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'François', 12, 'passager', '2025-07-18 12:32:40'),
(16, 'louise@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Louise', 10, 'passager', '2025-07-18 12:32:40'),
(17, 'paul@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Paul', 18, 'passager', '2025-07-18 12:32:40'),

(18, 'etienne@mail.com', '["ROLE_USER"]', '$2y$13$fXKj0wByXCydTcnVftW3KeAyUHL6h3nYOlzqQkJGS4wPofCWQhYUq', 'Etienne', 18, 'passager', '2025-07-18 12:32:40');


INSERT INTO vehicule (id, user_id, immatriculation, date_premiere_immatriculation, marque, modele, couleur, places_disponibles, energie)
VALUES
(1, 5, 'AB-AZE-FT', '2020-01-01', 'Renault', 'Zoe', 'blanc', 4, 'électrique'),
(2, 6, 'AB-345-FT', '2016-01-01', 'Peugeot', 'Expert', 'bleu', 2, 'diesel'),
(3, 7, 'CD-789-EF', '2021-03-10', 'Tesla', 'Model 3', 'noir', 4, 'électrique'),
(4, 8, 'EF-456-GH', '2019-07-22', 'Toyota', 'Prius', 'gris', 3, 'hybride'),
(5, 8, 'GH-789-IJ', '2022-02-15', 'BMW', 'i3', 'bleu', 2, 'électrique'),
(6, 9, 'IJ-101-KL', '2020-06-01', 'Citroën', 'C3', 'rouge', 4, 'essence'),
(7, 10, 'KL-202-MN', '2018-11-25', 'Ford', 'Focus', 'vert', 5, 'diesel'),
(8, 11, 'KM-103-FO', '2016-07-11', 'Citroën', 'C4', 'bleu', 3, 'électrique');


INSERT INTO trajet (id, chauffeur_id, vehicule_id, adresse_depart, adresse_arrivee, date_depart, date_arrivee, prix, places_restantes, statut, energie)
VALUES
(1, 5, 1, 'Paris', 'Orléans', '2026-01-10 08:00:00', '2026-01-10 11:00:00', 15, 3, 'trajet_a_venir', 'électrique'),
(2, 6, 2, 'Paris', 'Orléans', '2026-01-10 09:00:00', '2026-01-10 12:00:00', 13, 2, 'trajet_a_venir', 'diesel'),
(3, 7, 3, 'Paris', 'Orléans', '2026-01-10 10:00:00', '2026-01-10 13:00:00', 17, 0, 'trajet_a_venir', 'électrique'),

(5, 8, 4, 'Marseille', 'Lyon', '2026-01-10 11:00:00', '2026-01-10 14:00:00', 19, 2, 'trajet_a_venir', 'hybride'),
(6, 9, 6, 'Marseille', 'Lyon', '2026-01-10 13:00:00', '2026-01-10 16:00:00', 21, 0, 'trajet_a_venir', 'essence'),
(7, 10, 7, 'Marseille', 'Lyon', '2026-01-10 15:00:00', '2026-01-10 18:00:00', 18, 1, 'trajet_a_venir', 'diesel'),
(8, 11, 8, 'Marseille', 'Lyon', '2026-01-10 18:00:00', '2026-01-10 21:00:00', 17, 3, 'trajet_a_venir', 'électrique'),

(9, 5, 1, 'Montpellier', 'Avignon', '2026-01-10 20:00:00', '2026-01-10 23:00:00', 12, 2, 'trajet_a_venir', 'électrique'),
(10, 8, 4, 'Montpellier', 'Avignon', '2026-01-10 23:00:00', '2026-01-11 02:00:00', 13, 4, 'trajet_a_venir', 'hybride'),

(11, 11, 8, 'Bordeaux', 'Toulouse', '2026-01-11 02:00:00', '2026-01-11 05:00:00', 16, 3, 'trajet_a_venir', 'électrique'),

(12, 9, 6, 'Arcachon', 'Biarritz', '2026-01-11 05:00:00', '2026-01-11 08:00:00', 20, 2, 'trajet_a_venir', 'essence'),
(13, 7, 3, 'Arcachon', 'Biarritz', '2026-01-11 07:00:00', '2026-01-11 10:00:00', 22, 1, 'trajet_a_venir', 'électrique'),




(20, 5, 1, 'Paris', 'Lyon', '2024-12-01 08:00:00', '2024-12-01 10:00:00', 12, 0, 'trajet_termine', 'électrique'),
(21, 5, 1, 'Marseille', 'Nice', '2024-12-01 11:00:00', '2024-12-01 13:00:00', 10, 0, 'trajet_termine', 'électrique'),
(22, 5, 1, 'Toulouse', 'Bordeaux', '2024-12-01 14:00:00', '2024-12-01 16:00:00', 10, 0, 'trajet_termine', 'électrique'),
(23, 5, 1, 'Marseille', 'Nice', '2024-12-02 08:00:00', '2024-12-02 10:00:00', 10, 0, 'trajet_termine', 'électrique'),

(24, 6, 1, 'Toulouse', 'Bordeaux', '2024-12-02 11:00:00', '2024-12-02 13:00:00', 22, 0, 'trajet_termine', 'diesel'),
(25, 6, 2, 'Lille', 'Strasbourg', '2024-12-02 14:00:00', '2024-12-02 16:00:00', 8, 0, 'trajet_termine', 'diesel'),

(26, 7, 3, 'Toulouse', 'Bordeaux', '2024-12-03 08:00:00', '2024-12-03 10:00:00', 10, 0, 'trajet_annule', 'électrique'),
(27, 7, 3, 'Lille', 'Strasbourg', '2024-12-03 11:00:00', '2024-12-03 13:00:00', 9, 0, 'trajet_termine', 'électrique'),
(28, 7, 3, 'Paris', 'Lyon', '2024-12-03 14:00:00', '2024-12-03 16:00:00', 15, 0, 'trajet_termine', 'électrique'),
(29, 7, 3, 'Lille', 'Strasbourg', '2024-12-04 08:00:00', '2024-12-04 10:00:00', 12, 0, 'trajet_termine', 'électrique'),

(30, 8, 4, 'Paris', 'Lyon', '2024-12-04 11:00:00', '2024-12-04 13:00:00', 17, 0, 'trajet_termine', 'hybride'),
(31, 8, 5, 'Marseille', 'Nice', '2024-12-04 14:00:00', '2024-12-04 16:00:00', 12, 0, 'trajet_termine', 'électrique');



INSERT INTO reservation (id, trajet_id, passager_id, date_confirmation, statut, credits_utilises, commentaire_probleme)
VALUES
-- Réservations pour trajets à venir
(1, 1, 12, '2025-07-10 10:00:00', 'reservation_confirmee', 15, NULL),
(2, 1, 13, '2025-07-10 11:00:00', 'reservation_confirmee', 15, NULL),
(3, 2, 14, '2025-07-12 09:30:00', 'reservation_confirmee', 13, NULL),
(4, 3, 15, '2025-07-15 08:15:00', 'reservation_confirmee', 17, NULL),
(5, 5, 16, '2025-07-16 11:45:00', 'reservation_confirmee', 19, NULL),
(6, 5, 17, '2025-07-16 12:00:00', 'reservation_confirmee', 19, NULL),
(7, 6, 13, '2025-07-17 10:20:00', 'reservation_confirmee', 21, NULL),
(8, 7, 14, '2025-07-12 13:40:00', 'reservation_confirmee', 18, NULL),
(9, 8, 15, '2025-07-11 15:00:00', 'reservation_confirmee', 17, NULL),
(10, 8, 16, '2025-07-11 15:30:00', 'reservation_confirmee', 17, NULL),
(11, 9, 17, '2025-07-10 18:30:00', 'reservation_confirmee', 12, NULL),
(12, 10, 12, '2025-07-16 19:00:00', 'reservation_confirmee', 13, NULL),
(13, 11, 13, '2025-07-13 07:00:00', 'reservation_confirmee', 16, NULL),
(14, 12, 14, '2025-07-17 08:30:00', 'reservation_confirmee', 20, NULL),
(15, 13, 15, '2025-07-18 09:45:00', 'reservation_confirmee', 22, NULL),

-- Réservations pour trajets terminés
(16, 20, 12, '2024-11-28 10:00:00', 'reservation_terminee', 12, NULL),
(17, 20, 13, '2024-11-28 10:15:00', 'reservation_terminee', 12, NULL),
(18, 21, 14, '2024-11-29 09:00:00', 'reservation_terminee', 10, NULL),
(19, 22, 15, '2024-11-30 08:00:00', 'reservation_terminee', 10, NULL),
(20, 23, 16, '2024-11-30 14:00:00', 'reservation_terminee', 10, NULL),
(21, 24, 17, '2024-12-01 09:00:00', 'reservation_terminee', 22, NULL),
(22, 25, 12, '2024-12-01 11:00:00', 'reservation_terminee', 8, NULL),
(23, 27, 13, '2024-12-02 07:30:00', 'reservation_terminee', 9, NULL),
(24, 28, 14, '2024-12-02 10:00:00', 'reservation_terminee', 15, NULL),
(25, 29, 15, '2024-12-03 09:15:00', 'reservation_terminee', 12, NULL),
(26, 30, 16, '2024-12-03 11:30:00', 'reservation_terminee', 17, NULL),
(27, 31, 17, '2024-12-03 13:20:00', 'reservation_terminee', 12, NULL),

-- Réservation pour un trajet annulé
(28, 26, 14, '2024-12-01 12:30:00', 'reservation_confirmee', 10, NULL);


INSERT INTO avis (id, reservation_id, employe_valideur_id, note, commentaire, date_creation, statut_validation)
VALUES
(1, 13, 2, 5, 'Chauffeur très sympa, trajet agréable.', '2024-12-02 10:00:00', 'valide'),
(2, 14, 3, 4, 'Un peu de retard mais bon trajet.', '2024-12-02 10:30:00', 'valide'),
(3, 15, 4, 5, 'Ponctuel et véhicule propre.', '2024-12-02 11:00:00', 'en_attente'),
(4, 16, 2, 3, 'Chauffeur peu bavard mais correct.', '2024-12-02 11:30:00', 'refuse'),
(5, 17, 3, 2, 'Trop de musique forte, pas agréable.', '2024-12-02 12:00:00', 'valide'),
(6, 18, 4, 5, 'Super sympa et prudent.', '2024-12-02 12:30:00', 'valide'),
(7, 19, 2, 4, 'Bonne conduite, RAS.', '2024-12-03 09:00:00', 'en_attente'),
(8, 20, 3, 3, 'Itinéraire pas optimisé.', '2024-12-03 09:30:00', 'refuse');



INSERT INTO suspension (id, user_id, admin_id, date_suspension, motif)
VALUES
(1, 18, 1, '2025-05-20', 'Avis impolis');


INSERT INTO transaction (id, user_id, trajet_id, montant, type, date_transaction)
VALUES 
-- Réservations à venir (paiement + commission)
(1, 12, 1, 13, 'paiement', '2025-07-10 10:00:00'),
(2, 12, 1, 2, 'commission_plateforme', '2025-07-10 10:00:00'),
(3, 13, 1, 13, 'paiement', '2025-07-10 11:00:00'),
(4, 13, 1, 2, 'commission_plateforme', '2025-07-10 11:00:00'),
(5, 14, 2, 11, 'paiement', '2025-07-12 09:30:00'),
(6, 14, 2, 2, 'commission_plateforme', '2025-07-12 09:30:00'),
(7, 15, 3, 15, 'paiement', '2025-07-15 08:15:00'),
(8, 15, 3, 2, 'commission_plateforme', '2025-07-15 08:15:00'),
(9, 16, 5, 17, 'paiement', '2025-07-16 11:45:00'),
(10, 16, 5, 2, 'commission_plateforme', '2025-07-16 11:45:00'),
(11, 17, 5, 17, 'paiement', '2025-07-16 12:00:00'),
(12, 17, 5, 2, 'commission_plateforme', '2025-07-16 12:00:00'),
(13, 13, 6, 19, 'paiement', '2025-07-17 10:20:00'),
(14, 13, 6, 2, 'commission_plateforme', '2025-07-17 10:20:00'),
(15, 14, 7, 16, 'paiement', '2025-07-12 13:40:00'),
(16, 14, 7, 2, 'commission_plateforme', '2025-07-12 13:40:00'),
(17, 15, 8, 15, 'paiement', '2025-07-11 15:00:00'),
(18, 15, 8, 2, 'commission_plateforme', '2025-07-11 15:00:00'),
(19, 16, 8, 15, 'paiement', '2025-07-11 15:30:00'),
(20, 16, 8, 2, 'commission_plateforme', '2025-07-11 15:30:00'),
(21, 17, 9, 10, 'paiement', '2025-07-10 18:30:00'),
(22, 17, 9, 2, 'commission_plateforme', '2025-07-10 18:30:00'),
(23, 12, 10, 11, 'paiement', '2025-07-16 19:00:00'),
(24, 12, 10, 2, 'commission_plateforme', '2025-07-16 19:00:00'),
(25, 13, 11, 14, 'paiement', '2025-07-13 07:00:00'),
(26, 13, 11, 2, 'commission_plateforme', '2025-07-13 07:00:00'),
(27, 14, 12, 18, 'paiement', '2025-07-17 08:30:00'),
(28, 14, 12, 2, 'commission_plateforme', '2025-07-17 08:30:00'),
(29, 15, 13, 20, 'paiement', '2025-07-18 09:45:00'),
(30, 15, 13, 2, 'commission_plateforme', '2025-07-18 09:45:00'),

-- Réservations terminées (paiement + commission + paiement chauffeur)
(31, 12, 20, 10, 'paiement', '2024-11-28 10:00:00'),
(32, 12, 20, 2, 'commission_plateforme', '2024-11-28 10:00:00'),
(33, 5, 20, 10, 'paiement_chauffeur', '2024-12-01 10:00:00'),
(34, 13, 20, 10, 'paiement', '2024-11-28 10:15:00'),
(35, 13, 20, 2, 'commission_plateforme', '2024-11-28 10:15:00'),
(36, 5, 20, 10, 'paiement_chauffeur', '2024-12-01 10:00:00'),
(37, 14, 21, 8, 'paiement', '2024-11-29 09:00:00'),
(38, 14, 21, 2, 'commission_plateforme', '2024-11-29 09:00:00'),
(39, 5, 21, 8, 'paiement_chauffeur', '2024-12-01 13:00:00'),
(40, 15, 22, 8, 'paiement', '2024-11-30 08:00:00'),
(41, 15, 22, 2, 'commission_plateforme', '2024-11-30 08:00:00'),
(42, 5, 22, 8, 'paiement_chauffeur', '2024-12-01 16:00:00'),
(43, 16, 23, 8, 'paiement', '2024-11-30 14:00:00'),
(44, 16, 23, 2, 'commission_plateforme', '2024-11-30 14:00:00'),
(45, 5, 23, 8, 'paiement_chauffeur', '2024-12-02 10:00:00'),
(46, 17, 24, 20, 'paiement', '2024-12-01 09:00:00'),
(47, 17, 24, 2, 'commission_plateforme', '2024-12-01 09:00:00'),
(48, 6, 24, 20, 'paiement_chauffeur', '2024-12-02 13:00:00'),
(49, 12, 25, 6, 'paiement', '2024-12-01 11:00:00'),
(50, 12, 25, 2, 'commission_plateforme', '2024-12-01 11:00:00'),
(51, 6, 25, 6, 'paiement_chauffeur', '2024-12-02 16:00:00'),
(52, 13, 27, 7, 'paiement', '2024-12-02 07:30:00'),
(53, 13, 27, 2, 'commission_plateforme', '2024-12-02 07:30:00'),
(54, 7, 27, 7, 'paiement_chauffeur', '2024-12-03 13:00:00'),
(55, 14, 28, 13, 'paiement', '2024-12-02 10:00:00'),
(56, 14, 28, 2, 'commission_plateforme', '2024-12-02 10:00:00'),
(57, 7, 28, 13, 'paiement_chauffeur', '2024-12-03 16:00:00'),
(58, 15, 29, 10, 'paiement', '2024-12-03 09:15:00'),
(59, 15, 29, 2, 'commission_plateforme', '2024-12-03 09:15:00'),
(60, 7, 29, 10, 'paiement_chauffeur', '2024-12-04 10:00:00'),
(61, 16, 30, 15, 'paiement', '2024-12-03 11:30:00'),
(62, 16, 30, 2, 'commission_plateforme', '2024-12-03 11:30:00'),
(63, 8, 30, 15, 'paiement_chauffeur', '2024-12-04 13:00:00'),
(64, 17, 31, 10, 'paiement', '2024-12-03 13:20:00'),
(65, 17, 31, 2, 'commission_plateforme', '2024-12-03 13:20:00'),
(66, 8, 31, 10, 'paiement_chauffeur', '2024-12-04 16:00:00'),

-- Transactions pour trajet annulé
(67, 14, 26, 8, 'paiement', '2024-12-01 12:30:00'),
(68, 14, 26, 2, 'commission_plateforme', '2024-12-01 12:30:00'),
(69, 14, 26, 10, 'remboursement_passager_annulation_chauffeur', '2024-12-01 12:30:00');