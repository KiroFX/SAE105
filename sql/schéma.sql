CREATE DATABASE motus;
USE motus;



CREATE TABLE joueur (
    id_joueur INT AUTO_INCREMENT PRIMARY KEY,
    login VARCHAR(50) NOT NULL,
    mot_de_passe VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL
);

CREATE TABLE mot (
    id_mot INT AUTO_INCREMENT PRIMARY KEY,
    suite_carac VARCHAR(50) NOT NULL
);

CREATE TABLE partie (
    id_partie INT AUTO_INCREMENT PRIMARY KEY,
    id_joueur1 INT NOT NULL,
    id_joueur2 INT,
    id_mot INT NOT NULL,
    debut_partie DATETIME NOT NULL,
    fin_partie DATETIME,
    score_vainqueur INT,
    nombre_essais INT,
    vainqueur ENUM('joueur1', 'joueur2', 'égalité'),

    FOREIGN KEY (id_joueur1) REFERENCES joueur(id_joueur),
    FOREIGN KEY (id_joueur2) REFERENCES joueur(id_joueur),
    FOREIGN KEY (id_mot) REFERENCES mot(id_mot)
);