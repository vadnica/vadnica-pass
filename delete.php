<?php
session_start();
include_once "config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$id = $_GET['del'] ?? null;
$restore_id = $_GET['restore'] ?? null;
$category = $_GET['cat'] ?? null;
$permanent = $_GET['perm'] ?? 0;
$user_id = $_SESSION['user_id'];

if ($id) {
    try {
        if ($permanent == 1) {
            $sql = "DELETE FROM passwords WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id, $user_id]);
        } else {
            $sql = "UPDATE passwords SET is_deleted = 1 WHERE id = ? AND user_id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id, $user_id]);
        }
        
        $redirect = $category ? ("index.php?cat=" . $category) : "index.php";
        header("Location: " . $redirect);
    } catch (\PDOException $e) {
        die("Napaka pri brisanju: " . $e->getMessage());
    }
} elseif ($restore_id) {
    try {
        $sql = "UPDATE passwords SET is_deleted = 0 WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$restore_id, $user_id]);
        
        header("Location: trash.php");
    } catch (\PDOException $e) {
        die("Napaka pri obnovi: " . $e->getMessage());
    }
} else {
    header("Location: index.php");
}
