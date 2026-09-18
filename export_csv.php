<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once "config.php";
require_once "functions.php";

// Preverjanje prijave
if (!isset($_SESSION['user_id'])) {
    die("Dostop zavrnjen.");
}

// Čiščenje izhoda
if (ob_get_length()) ob_end_clean();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM passwords WHERE user_id = ? AND is_deleted = 0");
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();

// Nastavitev HTTP glav za prenos datoteke
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="safepass_export_' . date('Y-m-d') . '.csv"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');

// BOM za pravilno odpiranje v Excelu/Windows (UTF-8)
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Glava CSV datoteke
fputcsv($output, ["Group", "Title", "Username", "Password", "URL", "Notes", "TOTP"]);

foreach ($results as $row) {
    $group = "SafePass Export";
    $title = $row['name'];
    $username = decryptthis($row['user']);
    $password = decryptthis($row['pass']);
    $url = $row['url'];
    
    // Zbiranje dodatnih informacij za polje Notes
    $notes = "Category: " . $row['category'] . "\n";
    
    if (!empty($row['email'])) {
        $notes .= "Email: " . decryptthis($row['email']) . "\n";
    }
    
    // Bančne kartice
    if ($row['category'] === 'bank') {
        if (!empty($row['card_number'])) $notes .= "Card Number: " . decryptthis($row['card_number']) . "\n";
        if (!empty($row['card_expiry'])) $notes .= "Expiry: " . decryptthis($row['card_expiry']) . "\n";
        if (!empty($row['card_cvv'])) $notes .= "CVV: " . decryptthis($row['card_cvv']) . "\n";
    }
    
    // Omrežne nastavitve
    if ($row['category'] === 'network') {
        if (!empty($row['wifi_5g'])) $notes .= "WiFi 5GHz: " . decryptthis($row['wifi_5g']) . "\n";
        if (!empty($row['wifi_2_4g'])) $notes .= "WiFi 2.4GHz: " . decryptthis($row['wifi_2_4g']) . "\n";
        if (!empty($row['router_ip'])) $notes .= "Router IP: " . decryptthis($row['router_ip']) . "\n";
    }

    // 2FA / TOTP
    $totp = "";
    if (!empty($row['auth_code'])) {
        $auth_code = decryptthis($row['auth_code']);
        // Za CSV uvoz v KeePassXC je najbolje pripraviti otpauth URI, če ga znajo mapirati, 
        // ali pa pustiti kot navaden ključ.
        $totp = "otpauth://totp/" . rawurlencode($title) . "?secret=" . preg_replace('/\s+/', '', $auth_code) . "&issuer=SafePass";
    }

    fputcsv($output, [$group, $title, $username, $password, $url, $notes, $totp]);
}

fclose($output);
exit;
