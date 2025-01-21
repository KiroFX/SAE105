<?php
// Configuration de la base de données
define('DB_HOST', 'localhost');
define('DB_USER', 'root');  // Changez selon votre configuration
define('DB_PASS', '');      // Changez selon votre configuration
define('DB_NAME', 'motus');

// Configuration du jeu
define('MAX_ATTEMPTS', 6);      // Nombre maximum d'essais
define('TIMER_DURATION', 30);   // Durée du timer en secondes
define('MIN_WORD_LENGTH', 6);   // Longueur minimum des mots
define('MAX_WORD_LENGTH', 10);  // Longueur maximum des mots
?>