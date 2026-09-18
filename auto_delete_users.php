<?php
/**
 * Auto_delete_users.php
 * Avtomatsko brisanje uporabnikov po 3 dneh (razen admin).
 * Ta skripta se vključi v config.php.
 */

try {
    // 1. Samodejno čiščenje neaktivnih registracij (starejših od 10 minut)
    $stmt1 = $pdo->prepare("DELETE FROM users WHERE is_active = 0 AND created_at < (NOW() - INTERVAL 10 MINUTE)");
    $stmt1->execute();
    
    // 2. Izbriši demo uporabnike, ki so starejši od 3 dni in niso admin
    // Uporabimo created_at stolpec
    $stmt2 = $pdo->prepare("DELETE FROM users WHERE is_admin = 0 AND created_at < (NOW() - INTERVAL 3 DAY)");
    $stmt2->execute();
    
    $deletedCount = $stmt1->rowCount() + $stmt2->rowCount();
    if ($deletedCount > 0) {
        // Logiranje ali debug informacija, če je potrebno
        // error_log("Izbrisanih $deletedCount starih demo računov.");
    }
} catch (\PDOException $e) {
    // Napaka pri brisanju
    error_log("Napaka pri samodejnem brisanju uporabnikov: " . $e->getMessage());
}
