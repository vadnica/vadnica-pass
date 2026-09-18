<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once "config.php";
include_once "languages.php";
include_once "functions.php";

// Če ni podatkov o čakajoči 2FA prijavi, preusmeri na login
if (!isset($_SESSION['pending_2fa_user_id'])) {
    $token = isset($_GET['_ijt']) ? "?_ijt=" . $_GET['_ijt'] : "";
    header("Location: login.php" . $token);
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $code = trim($_POST['code'] ?? '');
    $secret = $_SESSION['pending_2fa_secret'];

    if (verifyTOTP($secret, $code)) {
        // Koda je pravilna, prijavi uporabnika
        $_SESSION['user_id'] = $_SESSION['pending_2fa_user_id'];
        $_SESSION['username'] = $_SESSION['pending_2fa_username'];
        $_SESSION['is_admin'] = $_SESSION['pending_2fa_is_admin'];
        
        // Počisti začasne podatke
        unset($_SESSION['pending_2fa_user_id']);
        unset($_SESSION['pending_2fa_username']);
        unset($_SESSION['pending_2fa_is_admin']);
        unset($_SESSION['pending_2fa_secret']);
        
        $token = isset($_GET['_ijt']) ? "?_ijt=" . $_GET['_ijt'] : "";
        header("Location: index.php" . $token);
        exit;
    } else {
        $error = $_SESSION['lang'] == 'sl' ? "Napačna koda. Poskusite znova." : "Invalid code. Please try again.";
    }
}

include "header.php";
include "navbar.php";
?>

<div class="auth-container auth-page">
    <div class="auth-stack">
        <div class="auth-card">
        <div class="auth-header">
            <h2><?php echo __('two_fa_title'); ?></h2>
            <p><?php echo __('two_fa_desc'); ?></p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="field">
                <label for="code"><?php echo __('two_fa_label'); ?></label>
                <input type="text" id="code" name="code" maxlength="6" placeholder="000000" required autofocus 
                       style="text-align: center; letter-spacing: 4px; font-size: 1.5rem; height: 60px; width: 100%; border-radius: 8px; border: 1px solid var(--border-color);">
            </div>
            <button type="submit" class="btn-primary" style="width: 100%; margin-top: 10px;"><?php echo __('two_fa_verify'); ?></button>
        </form>
        
        <div class="auth-footer" style="margin-top: 20px;">
            <a href="logout.php"><?php echo __('two_fa_cancel'); ?></a>
        </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('SafePass 2FA preverjanje pripravljeno.');
    const codeInput = document.getElementById('code');
    const form = document.querySelector('form');
    
    if (codeInput && form) {
        codeInput.addEventListener('input', function() {
            if (this.value.length === 6) {
                console.log('Zaznanih 6 številk, samodejno oddajam obrazec...');
                form.requestSubmit();
            }
        });
        
        form.addEventListener('submit', function(e) {
            const time = new Date().toLocaleTimeString();
            console.log('[' + time + '] JS: Oddajam 2FA kodo...');
            console.log('Trenutni URL: ' + window.location.href);
            console.log('Metoda: ' + e.target.method);
            console.log('Cilj (Action) v atrubutu: ' + e.target.getAttribute('action'));
            console.log('Cilj (Action) polni: ' + e.target.action);
            
            const btn = form.querySelector('button');
            if (btn) {
                btn.textContent = '<?php echo $_SESSION['lang'] == 'sl' ? 'Preverjanje...' : 'Checking...'; ?>';
                btn.style.opacity = '0.7';
            }
        });
    }
});
</script>

<?php include "footer.php"; ?>
