<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$gameManager = new GameManager();
$word = $gameManager->getRandomWord();

echo json_encode(['word' => $word['suite_carac']]);
?>