<?php
session_start();
include_once "config.php";
include_once "functions.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Pridobitev podatkov iz zahtevka
$category = $_POST['cat'] ?? $_GET['cat'] ?? 'misc';
$name = $_POST['name'] ?? '';
$user = $_POST['user'] ?? '';
$pass = $_POST['pass'] ?? '';
$email = $_POST['email'] ?? '';
$url = $_POST['url'] ?? '';
$card_number = $_POST['card_number'] ?? '';
$card_pin = $_POST['card_pin'] ?? '';
$card_cvv = $_POST['card_cvv'] ?? '';
$wifi_5g = $_POST['wifi_5g'] ?? '';
$wifi_2_4g = $_POST['wifi_2_4g'] ?? '';
$router_ip = $_POST['router_ip'] ?? '';
$auth_code = $_POST['auth_code'] ?? '';

// Šifriranje podatkov
$userencrypted = encryptthis($user);
$passencrypted = encryptthis($pass);
$mailencrypted = encryptthis($email);
$cardnum_encrypted = !empty($card_number) ? encryptthis($card_number) : null;
$cardpin_encrypted = !empty($card_pin) ? encryptthis($card_pin) : null;
$cardcvv_encrypted = !empty($card_cvv) ? encryptthis($card_cvv) : null;
$wifi5_encrypted = !empty($wifi_5g) ? encryptthis($wifi_5g) : null;
$wifi24_encrypted = !empty($wifi_2_4g) ? encryptthis($wifi_2_4g) : null;
$routerip_encrypted = !empty($router_ip) ? encryptthis($router_ip) : null;
$auth_encrypted = !empty($auth_code) ? encryptthis($auth_code) : null;

try {
    // Priprava in izvedba SQL stavka
    $sql = "INSERT INTO passwords (user_id, category, name, user, pass, email, url, card_number, card_pin, card_cvv, wifi_5g, wifi_2_4g, router_ip, auth_code) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$user_id, $category, $name, $userencrypted, $passencrypted, $mailencrypted, $url, $cardnum_encrypted, $cardpin_encrypted, $cardcvv_encrypted, $wifi5_encrypted, $wifi24_encrypted, $routerip_encrypted, $auth_encrypted]);

    $redirect = "index.php?cat=" . $category;
    
    // Dodajanje _ijt če obstaja v URL-ju
    $token = isset($_GET['_ijt']) ? "&_ijt=" . $_GET['_ijt'] : "";
    header("Location: " . $redirect . $token);
} catch (\PDOException $e) {
    die("ERROR: Napaka pri shranjevanju: " . $e->getMessage());
}
