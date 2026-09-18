<?php
// count_visit.php
session_start();

// Nastavimo pot do datoteke, kjer se bo shranjevalo število obiskov
$file = 'visitor_count.txt';

// Preberemo surovo vsebino POST zahtevka
$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Preverimo, če smo prejeli podatek o trajanju in če je večje od 30 sekund
if (isset($data['duration']) && $data['duration'] > 30) {

    // Ignoriraj admina (tebe)
    if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] == 1) {
        echo json_encode(['status' => 'ignored', 'message' => 'Admin visit']);
        exit;
    }

    // Ignoriraj lokalne obiske na strani strežnika (dodatna varovalka)
    $remote_addr = $_SERVER['REMOTE_ADDR'] ?? '';
    if ($remote_addr === '127.0.0.1' || $remote_addr === '::1') {
        echo json_encode(['status' => 'ignored', 'message' => 'Local visit']);
        exit;
    }

    // Če datoteka še ne obstaja, jo ustvarimo z začetno vrednostjo 0
    if (!file_exists($file)) {
        file_put_contents($file, '0');
    }

    // Odpremo datoteko za branje in pisanje
    $fp = fopen($file, 'r+');

    if ($fp) {
        // Zaklenemo datoteko, da preprečimo težave pri sočasnih zapisih
        if (flock($fp, LOCK_EX)) {
            $fsize = filesize($file);
            $count = (int)fread($fp, $fsize > 0 ? $fsize : 1);

            $count++; // Povečamo števec

            // Premaknemo kazalec na začetek in prepišemo vsebino
            rewind($fp);
            fwrite($fp, (string)$count);
            ftruncate($fp, strlen((string)$count));

            // Sprostimo zaklep
            flock($fp, LOCK_UN);
        }
        fclose($fp);
    }

    echo json_encode(['status' => 'success', 'new_count' => $count ?? 'unknown']);
} else {
    echo json_encode(['status' => 'ignored', 'message' => 'Duration too short or missing']);
}
