<?php
session_start();
include_once "config.php";

$error = '';
$success = '';

if (isset($_GET['code'])) {
    $code = $_GET['code'];
    
    try {
        // Preveri če koda obstaja in če še ni potekla (10 minut)
        $stmt = $pdo->prepare("SELECT id FROM users WHERE activation_code = ? AND is_active = 0 AND created_at > (NOW() - INTERVAL 10 MINUTE)");
        $stmt->execute([$code]);
        $user = $stmt->fetch();
        
        if ($user) {
            $stmt = $pdo->prepare("UPDATE users SET is_active = 1, activation_code = NULL WHERE id = ?");
            $stmt->execute([$user['id']]);
            $success = "Vaš račun je bil uspešno aktiviran! Zdaj se lahko <a href='login.php'>prijavite</a>.";
        } else {
            $error = "Neveljavna ali potekla aktivacijska koda. Registracije po 10 minutah neaktivnosti so samodejno odstranjene.";
        }
    } catch (\PDOException $e) {
        $error = "Napaka pri aktivaciji: " . $e->getMessage();
    }
} else {
    header("Location: index.php");
    exit;
}

include "header.php";
include "navbar.php";
?>

<div class="auth-container auth-page">
    <div class="auth-stack">
        <div class="auth-card">
        <div class="auth-header">
            <h1>Aktivacija računa</h1>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>
        
        <div class="auth-footer" style="margin-top: 20px; text-align: center;">
            <a href="login.php" class="btn-primary" style="display: inline-block; text-decoration: none;">Pojdi na prijavo</a>
        </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
