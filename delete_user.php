<?php
session_start();
include "config.php";

// Preverjanje če je uporabnik admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    die("Nimate dostopa do te operacije.");
}

$id = $_GET['id'] ?? null;

if ($id && $id != $_SESSION['user_id']) {
    try {
        // Ker imamo v bazi ON DELETE CASCADE za passwords, bo izbris uporabnika samodejno izbrisal tudi vsa njegova gesla.
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        
        header("Location: admin.php");
    } catch (\PDOException $e) {
        die("Napaka pri brisanju uporabnika: " . $e->getMessage());
    }
} else {
    header("Location: admin.php");
}
?>
