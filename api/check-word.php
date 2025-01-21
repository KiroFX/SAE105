<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$word = $data['word'] ?? '';

$gameManager = new GameManager();
$isValid = $gameManager->verifyWord($word);

echo json_encode(['valid' => $isValid]);