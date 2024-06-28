-- commandes utiles :
-- brew services start mysql
-- brew services info mysql
-- brew services list
-- mysql -u root -p

CREATE DATABASE IF NOT EXISTS projet_final_web;

USE projet_final_web;

CREATE TABLE IF NOT EXISTS utilisateurs (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    prenom VARCHAR(50) NOT NULL,
    nom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    adresse VARCHAR(255) NOT NULL,
    code_postal VARCHAR(5) NOT NULL,
    ville VARCHAR(100) NOT NULL,
    pays VARCHAR(100) NOT NULL,
    estVendeur BOOLEAN DEFAULT FALSE,
    lien_photo_piece_identite VARCHAR(255),
    IBAN VARCHAR(34),
    solde DECIMAL(10, 2) DEFAULT 0.00,
    date_inscription TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT chk_vendeur CHECK ((estVendeur = 0) OR (estVendeur = 1 AND lien_photo_piece_identite IS NOT NULL AND IBAN IS NOT NULL))
);

CREATE TABLE IF NOT EXISTS articles (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    description TEXT NOT NULL,
    prix DECIMAL(10, 2) NOT NULL,
    etat ENUM('neuf', 'comme neuf', 'bon état', 'état moyen', 'mauvais état') NOT NULL,
    type_pokemon ENUM('plante', 'feu', 'eau', 'insecte', 'normal', 'électrique', 'poison', 'fée', 'vol', 'combat', 'psy', 'sol', 'roche', 'spectre', 'acier', 'dragon', 'ténèbres', 'glace', 'autre') NOT NULL,
    stock INT NOT NULL,
    en_vedette BOOLEAN DEFAULT FALSE,
    lien_photo VARCHAR(255) NOT NULL,
    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    vendeur_id INT NOT NULL,
    FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id)
);

CREATE TABLE IF NOT EXISTS achats (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    utilisateur_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (utilisateur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (article_id) REFERENCES articles(id)
);

CREATE TABLE IF NOT EXISTS ventes (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    vendeur_id INT NOT NULL,
    article_id INT NOT NULL,
    quantite INT NOT NULL,
    date_vente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (vendeur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (article_id) REFERENCES articles(id)
);

CREATE TABLE IF NOT EXISTS messages (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    expediteur_id INT NOT NULL,
    destinataire_id INT NOT NULL,
    contenu TEXT NOT NULL,
    date_envoi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (expediteur_id) REFERENCES utilisateurs(id),
    FOREIGN KEY (destinataire_id) REFERENCES utilisateurs(id)
);

-- INSERT INTO utilisateurs (prenom, nom, email, mot_de_passe, adresse, code_postal, ville, pays, estVendeur, lien_photo_piece_identite, IBAN, solde)
-- VALUES
-- ('Jean', 'Dupont', 'jean@dupont.fr', 'Jean.12345', '1 rue de la Paix', '75001', 'Paris', 'France', TRUE, 'images/pieces_identite/jean_dupont.jpg', 'FR7630004000031234567890143', 0.00),
-- ('Marie', 'Durand', 'marie@durand.fr', 'Marie.12345', '2 rue de la Liberté', '69001', 'Lyon', 'France', FALSE, NULL, NULL, 0.00);

INSERT INTO articles (nom, description, prix, stock, etat, type_pokemon, en_vedette, lien_photo, date_creation, vendeur_id) 
VALUES
('Pikachu', 'Carte Pokémon Pikachu en excellent état. Parfait pour les collectionneurs et les fans de Pokémon.', 10.99, 50, 'neuf', 'électrique', FALSE, 'images/annonces/Pikachu.png', CURRENT_TIMESTAMP, 1), 
('Dracaufeu', 'Carte Pokémon Dracaufeu en très bon état. Un must-have pour les amateurs de Pokémon.', 19.99, 30, 'comme neuf', 'feu', TRUE, 'images/annonces/Dracaufeu.png', CURRENT_TIMESTAMP, 1), 
('Bulbizarre', 'Carte Pokémon Bulbizarre en bon état. Idéal pour compléter votre collection.', 5.99, 100, 'bon état', 'plante', FALSE, 'images/annonces/Bulbizarre.png', CURRENT_TIMESTAMP, 1), 
('Carapuce', 'Carte Pokémon Carapuce en état moyen. Parfait pour les joueurs occasionnels.', 7.99, 80, 'état moyen', 'eau', FALSE, 'images/annonces/Carapuce.png', CURRENT_TIMESTAMP, 1), 
('Rondoudou', 'Carte Pokémon Rondoudou en mauvais état. Convient aux collectionneurs à la recherche de pièces rares.', 3.99, 120, 'mauvais état', 'normal', FALSE, 'images/annonces/Rondoudou.png', CURRENT_TIMESTAMP, 1), 
('Mewtwo', 'Carte Pokémon Mewtwo en excellent état. Un ajout précieux à toute collection.', 15.99, 40, 'neuf', 'psy', TRUE, 'images/annonces/Mewtwo.png', CURRENT_TIMESTAMP, 1), 
('Évoli', 'Carte Pokémon Évoli en très bon état. Parfait pour les fans de Pokémon.', 8.99, 60, 'comme neuf', 'normal', FALSE, 'images/annonces/Evoli.png', CURRENT_TIMESTAMP, 1), 
('Léviator', 'Carte Pokémon Léviator en bon état. Un choix idéal pour les joueurs compétitifs.', 12.99, 70, 'bon état', 'eau', FALSE, 'images/annonces/Leviator.png', CURRENT_TIMESTAMP, 1), 
('Mackogneur', 'Carte Pokémon Mackogneur en état moyen. Convient aux collectionneurs et aux joueurs.', 6.99, 90, 'état moyen', 'combat', FALSE, 'images/annonces/Mackogneur.png', CURRENT_TIMESTAMP, 1), 
('Ronflex', 'Carte Pokémon Ronflex en mauvais état. Parfait pour les amateurs de pièces uniques.', 9.99, 50, 'mauvais état', 'normal', TRUE, 'images/annonces/Ronflex.png', CURRENT_TIMESTAMP, 1);

DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS ventes;
DROP TABLE IF EXISTS achats;
DROP TABLE IF EXISTS photos;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS utilisateurs;
DROP DATABASE IF EXISTS projet_final_web;

-- FR1420041010050500013M02606