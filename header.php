<?php
header('Content-Type: text/html; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once "config.php";
include_once "languages.php";
include_once "functions.php";

// Seznam javnih strani, ki ne zahtevajo prijave
$public_pages = ['login.php', 'register.php', 'verify2fa.php', 'recovery.php'];
$current_page = basename($_SERVER['PHP_SELF']);

// Preverjanje seje
if (!isset($_SESSION['user_id']) && !in_array($current_page, $public_pages)) {
    $token = isset($_GET['_ijt']) ? "?_ijt=" . $_GET['_ijt'] : "";
    header("Location: login.php" . $token);
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?php echo $_SESSION['lang'] ?? 'sl'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SafePass - Modern Password Manager</title>
    <link rel="icon" type="image/x-icon" href="favicon.ico?v=3">
    <link rel="shortcut icon" type="image/x-icon" href="favicon.ico?v=3">
    <link rel="icon" type="image/png" sizes="32x32" href="icons/favicon-32x32.png?v=3">
    <link rel="icon" type="image/png" sizes="16x16" href="icons/favicon-16x16.png?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="icons/apple-touch-icon.png?v=3">
    <link rel="manifest" href="site.webmanifest?v=3">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <script src="header.js"></script>
    <script src="navbar.js"></script>
    <script src="password_generator.js"></script>
</head>
<body>
    <div class="app-layout">
