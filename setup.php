<?php
include "config.php";
include "functions.php";

try {
    // 1. Ustvarjanje tabele za uporabnike
    $sqlUsers = "CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL,
        is_active TINYINT(1) DEFAULT 0,
        activation_code VARCHAR(100),
        is_admin TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        totp_secret VARCHAR(255) DEFAULT NULL,
        encrypted_dek VARCHAR(255) DEFAULT NULL,
        recovery_dek VARCHAR(255) DEFAULT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sqlUsers);
    echo "Tabela 'users' je pripravljena.<br>";

    // Dodajanje is_active in activation_code stolpcev če še ne obstajata
    $checkActive = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_active'");
    if ($checkActive->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 0 AFTER email");
        $pdo->exec("ALTER TABLE users ADD COLUMN activation_code VARCHAR(100) AFTER is_active");
        echo "Stolpca 'is_active' in 'activation_code' sta bila dodana v tabelo 'users'.<br>";
    }

    // Dodajanje is_admin stolpca če še ne obstaja (za obstoječe tabele)
    $checkAdmin = $pdo->query("SHOW COLUMNS FROM users LIKE 'is_admin'");
    if ($checkAdmin->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN is_admin TINYINT(1) DEFAULT 0 AFTER email");
        echo "Stolpec 'is_admin' je bil dodan v tabelo 'users'.<br>";
    }

    // Dodajanje totp_secret stolpca če še ne obstaja
    $checkTotp = $pdo->query("SHOW COLUMNS FROM users LIKE 'totp_secret'");
    if ($checkTotp->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN totp_secret VARCHAR(255) DEFAULT NULL AFTER is_admin");
        echo "Stolpec 'totp_secret' je bil dodan v tabelo 'users'.<br>";
    } else {
        // Posodobi dolžino če že obstaja
        $pdo->exec("ALTER TABLE users MODIFY COLUMN totp_secret VARCHAR(255) DEFAULT NULL");
        echo "Stolpec 'totp_secret' je bil posodobljen na dolžino 255.<br>";
    }

    // Dodajanje encrypted_dek stolpca če še ne obstaja
    $checkDek = $pdo->query("SHOW COLUMNS FROM users LIKE 'encrypted_dek'");
    if ($checkDek->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN encrypted_dek VARCHAR(255) DEFAULT NULL AFTER totp_secret");
        echo "Stolpec 'encrypted_dek' je bil dodan v tabelo 'users'.<br>";
    }

    // Dodajanje recovery_dek stolpca če še ne obstaja
    $checkRec = $pdo->query("SHOW COLUMNS FROM users LIKE 'recovery_dek'");
    if ($checkRec->rowCount() == 0) {
        $pdo->exec("ALTER TABLE users ADD COLUMN recovery_dek VARCHAR(255) DEFAULT NULL AFTER encrypted_dek");
        echo "Stolpec 'recovery_dek' je bil dodan v tabelo 'users'.<br>";
    }

    // 2. Ustvarjanje tabele za gesla (če še ne obstaja)
    $sqlPasswords = "CREATE TABLE IF NOT EXISTS passwords (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_id INT UNSIGNED,
        category VARCHAR(50) NOT NULL,
        name VARCHAR(200) NOT NULL,
        user VARCHAR(255) NOT NULL,
        pass CHAR(255),
        email CHAR(200),
        url VARCHAR(200),
        card_number VARCHAR(255) DEFAULT NULL,
        card_pin VARCHAR(255) DEFAULT NULL,
        card_cvv VARCHAR(255) DEFAULT NULL,
        wifi_5g VARCHAR(255) DEFAULT NULL,
        wifi_2_4g VARCHAR(255) DEFAULT NULL,
        router_ip VARCHAR(255) DEFAULT NULL,
        auth_code VARCHAR(255) DEFAULT NULL,
        is_deleted TINYINT(1) DEFAULT 0,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    
    $pdo->exec($sqlPasswords);
    echo "Tabela 'passwords' je pripravljena.<br>";

    // Dodajanje novih stolpcev če še ne obstajajo
    $newColumns = [
        'card_number' => "ALTER TABLE passwords ADD COLUMN card_number VARCHAR(255) DEFAULT NULL AFTER url",
        'card_pin' => "ALTER TABLE passwords ADD COLUMN card_pin VARCHAR(255) DEFAULT NULL AFTER card_number",
        'card_cvv' => "ALTER TABLE passwords ADD COLUMN card_cvv VARCHAR(255) DEFAULT NULL AFTER card_pin",
        'wifi_5g' => "ALTER TABLE passwords ADD COLUMN wifi_5g VARCHAR(255) DEFAULT NULL AFTER card_cvv",
        'wifi_2_4g' => "ALTER TABLE passwords ADD COLUMN wifi_2_4g VARCHAR(255) DEFAULT NULL AFTER wifi_5g",
        'router_ip' => "ALTER TABLE passwords ADD COLUMN router_ip VARCHAR(255) DEFAULT NULL AFTER wifi_2_4g",
        'auth_code' => "ALTER TABLE passwords ADD COLUMN auth_code VARCHAR(255) DEFAULT NULL AFTER router_ip",
        'is_deleted' => "ALTER TABLE passwords ADD COLUMN is_deleted TINYINT(1) DEFAULT 0 AFTER auth_code"
    ];

    foreach ($newColumns as $col => $sql) {
        $checkCol = $pdo->query("SHOW COLUMNS FROM passwords LIKE '$col'");
        if ($checkCol->rowCount() == 0) {
            $pdo->exec($sql);
            echo "Stolpec '$col' je bil dodan v tabelo 'passwords'.<br>";
        }
    }

    // 3. Preverimo če stolpec user_id že obstaja v passwords, če ne, ga dodamo
    $checkColumn = $pdo->query("SHOW COLUMNS FROM passwords LIKE 'user_id'");
    if ($checkColumn->rowCount() == 0) {
        $pdo->exec("ALTER TABLE passwords ADD COLUMN user_id INT UNSIGNED AFTER id");
        $pdo->exec("ALTER TABLE passwords ADD CONSTRAINT fk_user_pass FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE");
        echo "Stolpec 'user_id' je bil dodan v tabelo 'passwords'.<br>";
    }

    echo "<h2>Namestitev baze uspešna!</h2>";
    echo "<a href='index.php'>Nazaj na glavno stran</a>";

} catch (\PDOException $e) {
    die("Napaka pri nastavitvi baze: " . $e->getMessage());
}
