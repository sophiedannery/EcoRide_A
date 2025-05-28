INSERT INTO user (email, roles, password, pseudo, credits, statut, date_creation) 
VALUES
('admin@ecoride.com', '["ROLE_ADMIN"]', 'azerty', 'Administrateur', NULL, 'admin', '2025-01-10 09:00:00'),
('employe1@ecoride.com', '["ROLE_EMPLOYEE"]', 'azerty', 'Employe1', NULL, 'employe', '2025-02-15 19:00:00'),
('employe2@ecoride.com', '["ROLE_EMPLOYEE"]', 'azerty', 'Employe2', NULL, 'employe', '2025-02-15 20:00:00'),
('maddie@ecoride.com', '["ROLE_USER"]', 'azerty', 'Maddie', 20, 'passager', '2025-02-15 20:00:00'),
('raph@ecoride.com', '["ROLE_USER"]', 'azerty', 'Raph', 100, 'passager', '2025-02-12 20:00:00'),
('sophie@ecoride.com', '["ROLE_USER"]', 'azerty', 'Sophie', 80, 'passager', '2025-02-15 20:00:00'),
('laura@ecoride.com', '["ROLE_USER"]', 'azerty', 'Laura', 300, 'passager', '2025-03-25 20:00:00'),
('cle@ecoride.com', '["ROLE_USER"]', 'azerty', 'Cle', 180, 'passager', '2025-04-30 20:00:00'),
('vin@ecoride.com', '["ROLE_USER"]', 'azerty', 'Vin', 120, 'passager', '2025-05-02 20:00:00');

INSERT INTO preference (libelle) 
VALUES
('Fumeur'),
('Non-fumeur'),
('Avec animaux'),
('Sans animaux');

INSERT INTO preference_user (preference_id, user_id)
VALUES
('1', '5'),
('3', '5'),
('21', '4'),
('1', '6'),
('3', '8'),
('1', '8'),
('2', '9'),
('1', '10');

INSERT INTO vehicule (user_id, immatriculation, date_premiere_immatriculation, marque, modele, couleur, places_disponibles, energie)