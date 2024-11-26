-- Création de la base de données

CREATE DATABASE IF NOT EXISTS blog_collaboratif;

USE blog_collaboratif;



-- Table des utilisateurs

CREATE TABLE utilisateurs (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nom VARCHAR(100) NOT NULL,

    email VARCHAR(100) NOT NULL UNIQUE,

    mot_de_passe VARCHAR(255) NOT NULL,

    bio TEXT,

    photo VARCHAR(255),

    compétences VARCHAR(255),

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);



-- Table des catégories

CREATE TABLE categories (

    id INT AUTO_INCREMENT PRIMARY KEY,

    nom_catégorie VARCHAR(100) NOT NULL UNIQUE,

    date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP

);



-- Table des articles

CREATE TABLE articles (

    id INT AUTO_INCREMENT PRIMARY KEY,

    titre VARCHAR(200) NOT NULL,

    contenu TEXT NOT NULL,

    date_publication TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    id_auteur INT NOT NULL,

    id_categorie INT NOT NULL,

    FOREIGN KEY (id_auteur) REFERENCES utilisateurs(id) ON DELETE CASCADE,

    FOREIGN KEY (id_categorie) REFERENCES categories(id) ON DELETE SET NULL

);



-- Table des commentaires

CREATE TABLE commentaires (

    id INT AUTO_INCREMENT PRIMARY KEY,

    contenu TEXT NOT NULL,

    date_commentaire TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    id_utilisateur INT NOT NULL,

    id_article INT NOT NULL,

    FOREIGN KEY (id_utilisateur) REFERENCES utilisateurs(id) ON DELETE CASCADE,

    FOREIGN KEY (id_article) REFERENCES articles(id) ON DELETE CASCADE

);



-- Remplissage de données de test (optionnel)

-- Ajout d'utilisateurs

INSERT INTO utilisateurs (nom, email, mot_de_passe, bio, compétences) VALUES

('Jean Dupont', 'jean.dupont@example.com', '$2y$10$abcdefghijklmnoqrstuv', 'Développeur PHP passionné', 'PHP, MySQL, JavaScript'),

('Marie Curie', 'marie.curie@example.com', '$2y$10$abcdefghijklmnoqrstuv', 'Data Scientist', 'Python, Machine Learning');



-- Ajout de catégories

INSERT INTO categories (nom_catégorie) VALUES

('Programmation Web'),

('Intelligence Artificielle'),

('Bases de Données'),

('Sécurité Informatique');



-- Ajout d'articles

INSERT INTO articles (titre, contenu, id_auteur, id_categorie) VALUES

('Introduction à PHP', 'PHP est un langage serveur...', 1, 1),

('Les bases de données relationnelles', 'Une introduction aux bases SQL...', 2, 3);



-- Ajout de commentaires

INSERT INTO commentaires (contenu, id_utilisateur, id_article) VALUES

('Super article, très informatif !', 2, 1),

('Merci pour ces explications !', 1, 2);

