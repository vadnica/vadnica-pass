<?php
session_start();
include_once "config.php";
include_once "functions.php";

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    exit;
}

$secret = $_GET['secret'] ?? '';

if (empty($secret)) {
    echo json_encode(['code' => '------', 'timeLeft' => 0]);
    exit;
}

// Skrivnost je že bila dekriptirana v renderPasswordRow in poslana sem
// (ali pa jo moramo tu dekriptirati, če bi pošiljali šifrirano - 
// vendar jo pošiljamo preko data-secret v HTML, ki je že dekriptirana vrednost)

$code = getTOTPCode($secret);
$timeLeft = 30 - (time() % 30);

echo json_encode([
    'code' => $code,
    'timeLeft' => $timeLeft
]);
