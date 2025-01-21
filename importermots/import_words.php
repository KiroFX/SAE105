<?php
// Connexion à la base de données
$mysqli = new mysqli("localhost", "root", "", "motus");

if ($mysqli->connect_error) {
    die("Erreur de connexion : " . $mysqli->connect_error);
}

// Ouvrir le fichier contenant les mots
$file = fopen("words.txt", "r");

if ($file) {
    // Préparer la requête d'insertion
    $stmt = $mysqli->prepare("INSERT INTO mot (suite_carac) VALUES (?)");
    if ($stmt === false) {
        die('Erreur de préparation de la requête : ' . $mysqli->error);
    }

    // Lire chaque ligne du fichier et insérer le mot
    while (($line = fgets($file)) !== false) {
        $word = trim($line); // Supprime les espaces autour du mot

        // Lier le mot à la requête et l'exécuter
        $stmt->bind_param("s", $word);
        if (!$stmt->execute()) {
            die('Erreur d\'exécution de la requête : ' . $stmt->error);
        }
    }

    fclose($file);
    echo "Importation des mots terminée avec succès !";
} else {
    die("Impossible d'ouvrir le fichier words.txt. Vérifiez les permissions ou le chemin.");
}

$mysqli->close();
?>