<?php
require_once 'includes/config.php';

if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

// Récupération du mot aléatoire
$stmt = $db->query("SELECT id_mot, suite_carac FROM mot ORDER BY RAND() LIMIT 1");
$mot = $stmt->fetch();

// Création nouvelle partie
$stmt = $db->prepare("
    INSERT INTO partie (id_joueur1, id_mot, debut_partie) 
    VALUES (?, ?, NOW())
");
$stmt->execute([$_SESSION['user']['id_joueur'], $mot['id_mot']]);
$partieId = $db->lastInsertId();

$_SESSION['game'] = [
    'partie_id' => $partieId,
    'mot' => $mot['suite_carac'],
    'essais' => 0,
    'joueur_actuel' => 1,
    'debut_tour' => time()
];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Motus - Jeu</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h1>Motus - Joueur <?= $_SESSION['game']['joueur_actuel'] ?></h1>
    <div id="game-board"></div>
    <form id="word-form">
        <input type="text" id="word-input" maxlength="10" required>
        <button type="submit">Valider</button>
    </form>
    <div id="timer">30</div>
    <script src="assets/js/game.js"></script>
</body>
</html>