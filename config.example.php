<?php
// PRIMER KONFIGURACIJE - kopiraj to datoteko v db.php in izpolni svoje podatke!
// NE POŠILJAJ TE DATOTEKE Z VSEBINAMI NA GITHUB!

// === PODATKI ZA BAZO ===
$host = ''; // npr. 'localhost'
$user = ''; // uporabniško ime baze
$pass = ''; // geslo za bazo
$base = ''; // ime baze

// === PHPMailer SMTP nastavitve ===
define('SMTP_HOST', ''); // npr. 'smtp.gmail.com'
define('SMTP_USER', ''); // email za SMTP
define('SMTP_PASS', ''); // geslo za SMTP (ali app password)
define('SMTP_PORT', 465); // 465 za SSL, 587 za TLS
define('SMTP_FROM', ''); // email, ki bo kot pošiljatelj
define('SMTP_FROM_NAME', 'SafePass Manager');

// === KLJUČ ZA ŠIFRIRANJE (AES-256) ===
// To je TVOJ glavni ključ! Če ga izgubiš, so vsa gesla v bazi nedostopna.
// Generiraj naključen niz (npr. 32 znakov ali več).
define('ENCRYPTION_KEY', '');

// === PREVERBA ===
if (empty($host) || empty($user) || empty($pass) || empty($base) || empty(ENCRYPTION_KEY)) {
    die("Napaka: Konfiguracija ni izpolnjena! Kopiraj config.example.php v db.php in izpolni podatke.");
}

try {
    $dsn = "mysql:host=$host;dbname=$base;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);

    // Samodejno brisanje starih računov in neaktivnih registracij
    include_once "auto_delete_users.php";
} catch (\PDOException $e) {
    die("Povezava z bazo ni uspela: " . $e->getMessage());
}
