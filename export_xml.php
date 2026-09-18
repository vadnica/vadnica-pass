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

// Čiščenje izhoda, da preprečimo kakršne koli predhodne izpise
if (ob_get_length()) ob_end_clean();

$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM passwords WHERE user_id = ? AND is_deleted = 0");
$stmt->execute([$user_id]);
$results = $stmt->fetchAll();

/**
 * Pomožna funkcija za dodajanje String vozlišča v XML
 */
function addEntryString($parent, $key, $value, $protect = false) {
    $string = $parent->addChild('String');
    $string->addChild('Key', $key);
    $valNode = $string->addChild('Value');
    if ($value !== null && $value !== '') {
        $valNode[0] = $value;
    }
}

/**
 * Pomožna funkcija za generiranje UUID (Base64)
 */
function generateUUID() {
    return base64_encode(random_bytes(16));
}

// Ustvarjanje XML strukture
$xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8" standalone="yes"?><KeePassFile></KeePassFile>');
$meta = $xml->addChild('Meta');
$meta->addChild('Generator', 'SafePass Manager');
$meta->addChild('DatabaseName', 'SafePass Export');

$root = $xml->addChild('Root');
$group = $root->addChild('Group');
$group->addChild('UUID', generateUUID());
$group->addChild('Name', 'SafePass Export');

foreach ($results as $row) {
    $entry = $group->addChild('Entry');
    $entry->addChild('UUID', generateUUID());
    $entry->addChild('IconID', '0');
    
    // Osnovna polja
    addEntryString($entry, 'Title', $row['name']);
    addEntryString($entry, 'UserName', decryptthis($row['user']));
    addEntryString($entry, 'Password', decryptthis($row['pass']), true);
    addEntryString($entry, 'URL', $row['url']);
    
    // Zbiranje dodatnih informacij za polje Notes
    $notes = "Category: " . $row['category'] . "\n";
    
    // E-pošta
    if (!empty($row['email'])) {
        $email = decryptthis($row['email']);
        addEntryString($entry, 'Email', $email);
        $notes .= "Email: " . $email . "\n";
    }

    // Kartice
    if (!empty($row['card_number'])) {
        $notes .= "--- Bank Card ---\n";
        $notes .= "Card Number: " . decryptthis($row['card_number']) . "\n";
        $notes .= "PIN: " . decryptthis($row['card_pin']) . "\n";
        $notes .= "CVV: " . decryptthis($row['card_cvv']) . "\n";
    }

    // WiFi in omrežje
    if (!empty($row['wifi_5g']) || !empty($row['wifi_2_4g']) || !empty($row['router_ip'])) {
        $notes .= "--- Network ---\n";
        if (!empty($row['wifi_5g'])) $notes .= "WiFi 5GHz: " . decryptthis($row['wifi_5g']) . "\n";
        if (!empty($row['wifi_2_4g'])) $notes .= "WiFi 2.4GHz: " . decryptthis($row['wifi_2_4g']) . "\n";
        if (!empty($row['router_ip'])) $notes .= "Router IP: " . decryptthis($row['router_ip']) . "\n";
    }
    
    addEntryString($entry, 'Notes', trim($notes));

    // 2FA / TOTP (KeePassXC prepozna polje 'otp')
    if (!empty($row['auth_code'])) {
        $secret = preg_replace('/\s+/', '', decryptthis($row['auth_code']));
        $secret = strtoupper($secret);
        
        $label = rawurlencode($row['name']);
        $otp_uri = "otpauth://totp/SafePass:{$label}?secret={$secret}&issuer=SafePass";
        
        // Za KeePassXC
        addEntryString($entry, 'otp', $otp_uri, true);
        
        // Za KeePass (original) z KeeOtp2 pluginom
        addEntryString($entry, 'TimeOtp-Secret-Base32', $secret, true);
        addEntryString($entry, 'TimeOtp-Length', '6');
        addEntryString($entry, 'TimeOtp-Period', '30');
    }
    
    // Časovni žigi
    $times = $entry->addChild('Times');
    $now = date('Y-m-d\TH:i:s\Z');
    $created = date('Y-m-d\TH:i:s\Z', strtotime($row['created_at']));
    
    $times->addChild('CreationTime', $created);
    $times->addChild('LastModificationTime', $created);
    $times->addChild('LastAccessTime', $now);
    $times->addChild('ExpiryTime', $now);
    $times->addChild('Expires', 'False');
    $times->addChild('UsageCount', '0');
    $times->addChild('LocationChanged', $created);
}

// Nastavitev HTTP glav za prenos datoteke
header('Content-Type: application/xml; charset=utf-8');
header('Content-Disposition: attachment; filename="safepass_export_' . date('Y-m-d') . '.xml"');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

echo $xml->asXML();
exit;
