CREATE TABLE user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(180) NOT NULL UNIQUE,
    roles JSON NOT NULL CHECK (JSON_VALID(roles)), 
    password VARCHAR(255) NOT NULL,
    pseudo VARCHAR(255) NOT NULL,
    credits INT DEFAULT NULL,
    statut VARCHAR(255) NOT NULL,
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    photo_filename VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicule (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    immatriculation VARCHAR(255) NOT NULL,
    date_premiere_immatriculation DATE NOT NULL, 
    marque VARCHAR(255) NOT NULL, 
    modele VARCHAR(255) NOT NULL,
    couleur VARCHAR(255) NOT NULL,
    places_disponibles INT NOT NULL,
    energie VARCHAR(255) NOT NULL, 
    INDEX idx_vehicule_user (user_id), 
    CONSTRAINT fk_vehicule_user
        FOREIGN KEY (user_id)
        REFERENCES user(id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE trajet (
    id INT AUTO_INCREMENT PRIMARY KEY,
    chauffeur_id INT NOT NULL, 
    vehicule_id INT NOT NULL, 
    adresse_depart VARCHAR(255) NOT NULL,
    adresse_arrivee VARCHAR(255) NOT NULL,
    date_depart DATETIME NOT NULL, 
    date_arrivee DATETIME NOT NULL,
    prix INT NOT NULL, 
    places_restantes INT NOT NULL, 
    statut VARCHAR(255) NOT NULL, 
    energie VARCHAR(255) NOT NULL, 
    INDEX idx_trajet_chauffeur (chauffeur_id), 
    INDEX idx_trajet_vehicule (vehicule_id), 
    CONSTRAINT fk_trajet_chauffeur
        FOREIGN KEY (chauffeur_id)
        REFERENCES user(id)
        ON DELETE RESTRICT, 
    CONSTRAINT fk_trajet_vehicule
        FOREIGN KEY (vehicule_id)
        REFERENCES vehicule(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reservation (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trajet_id INT NOT NULL, 
    passager_id INT NOT NULL, 
    date_confirmation DATETIME NOT NULL, 
    statut VARCHAR(255) NOT NULL, 
    credits_utilises INT NOT NULL, 
    commentaire_probleme LONGTEXT DEFAULT NULL,
    INDEX idx_reservation_trajet (trajet_id), 
    INDEX idx_reservation_passager(passager_id),
    CONSTRAINT fk_reservation_trajet
        FOREIGN KEY (trajet_id)
        REFERENCES trajet(id)
        ON DELETE CASCADE, 
    CONSTRAINT fk_reservation_passager
        FOREIGN KEY (passager_id)
        REFERENCES user(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE avis (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    reservation_id INT NOT NULL UNIQUE,
    employe_valideur_id INT DEFAULT NULL,
    note INT NOT NULL, 
    commentaire LONGTEXT DEFAULT NULL, 
    date_creation DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    statut_validation VARCHAR(255) NOT NULL, 
    INDEX idx_avis_reservation (reservation_id),
    INDEX idx_avis_valideur (employe_valideur_id),
    CONSTRAINT fk_avis_reservation
        FOREIGN KEY (reservation_id) 
        REFERENCES reservation(id)
        ON DELETE CASCADE, 
    CONSTRAINT fk_avis_valideur
        FOREIGN KEY (employe_valideur_id) 
        REFERENCES user(id)
        ON DELETE SET NULL        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE suspension (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    admin_id INT NOT NULL, 
    date_suspension DATE NOT NULL, 
    motif LONGTEXT DEFAULT NULL, 
    INDEX idx_suspension_user (user_id),
    INDEX idx_suspension_admin (admin_id),
    CONSTRAINT fk_suspension_user
        FOREIGN KEY (user_id)
        REFERENCES user(id)
        ON DELETE CASCADE, 
    CONSTRAINT fk_suspension_admin
        FOREIGN KEY (admin_id)
        REFERENCES user(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE transaction (
    id INT AUTO_INCREMENT PRIMARY KEY, 
    user_id INT NOT NULL, 
    trajet_id INT NOT NULL, 
    montant INT NOT NULL, 
    date_transaction DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
    type VARCHAR(255) NOT NULL, 
    INDEX idx_transaction_user (user_id),
    INDEX idx_transaction_trajet (trajet_id),
    CONSTRAINT fk_transaction_user
        FOREIGN KEY (user_id)
        REFERENCES user(id)
        ON DELETE CASCADE,
    CONSTRAINT fk_transaction_trajet
        FOREIGN KEY (trajet_id)
        REFERENCES trajet(id)
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;