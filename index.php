<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MOTUS</title>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="game-container">
        <div class="header">
            <div class="score">0</div>
            <div class="timer">30</div>
            <div class="score">0</div>
        </div>
        
        <div class="game-grid">
            <!-- Les lignes seront générées en JS -->
        </div>

        <div class="input-zone">
            <input type="text" id="word-input" maxlength="8" placeholder="Entrez votre mot">
            <button id="submit-word">Valider</button>
        </div>

        <div class="player-info">
            <div class="current-player">Joueur 1</div>
            <div class="attempts">Essai 1/6</div>
        </div>
    </div>

    <script src="assets/js/game.js"></script>
</body>
</html>