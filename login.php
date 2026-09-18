<?php
ob_start();
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "config.php";
include_once "languages.php";
include_once "functions.php";

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = $_SESSION['lang'] == 'sl' ? "Vnesite uporabniško ime ali e-pošto in geslo." : "Please enter username or email and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, is_admin, is_active, totp_secret, encrypted_dek FROM users WHERE username = ? OR email = ? ORDER BY is_active DESC LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Dešifriranje DEK ključa z geslom
                if (!empty($user['encrypted_dek'])) {
                    $password_kek_b64 = base64_encode(hash('sha256', $password, true));
                    $decrypted_dek = decryptthis($user['encrypted_dek'], $password_kek_b64);
                    if ($decrypted_dek !== false) {
                        $_SESSION['dek'] = $decrypted_dek;
                    }
                }

                if ($user['is_active'] == 0) {
                    $error = $_SESSION['lang'] == 'sl' ? "Vaš račun še ni bil aktiviran. Preverite svojo e-pošto." : "Your account has not been activated yet. Check your email.";
                } elseif (!empty($user['totp_secret'])) {
                    $_SESSION['pending_2fa_user_id'] = $user['id'];
                    $_SESSION['pending_2fa_username'] = $user['username'];
                    $_SESSION['pending_2fa_is_admin'] = $user['is_admin'];
                    
                    // Dešifriraj totp_secret (poskusi dešifrirati, če ne gre, uporabi surovo vrednost)
                    $decrypted = decryptthis($user['totp_secret']);
                    $_SESSION['pending_2fa_secret'] = ($decrypted !== false) ? $decrypted : $user['totp_secret'];
                    
                    $token = isset($_GET['_ijt']) ? "?_ijt=" . $_GET['_ijt'] : "";
                    header("Location: verify2fa.php" . $token);
                    exit;
                } else {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['is_admin'] = $user['is_admin'];
                    
                    $token = isset($_GET['_ijt']) ? "?_ijt=" . $_GET['_ijt'] : "";
                    header("Location: index.php" . $token);
                    exit;
                }
            } else {
                $error = $_SESSION['lang'] == 'sl' ? "Napačno uporabniško ime ali geslo." : "Incorrect username or password.";
            }
        } catch (\PDOException $e) {
            $error = ($_SESSION['lang'] == 'sl' ? "Napaka pri prijavi: " : "Login error: ") . $e->getMessage();
        }
    }
}

include "header.php";
include "navbar.php";
?>

<div class="auth-container auth-page">
    <div class="auth-stack">
        <div class="test-notice-card">
            <div class="test-notice-title">⚠️ <?php echo __('test_warning_title'); ?></div>
            <p><?php echo __('test_warning_text'); ?></p>
        </div>
        <div class="auth-card">
            <div class="auth-header">
                <p><?php echo __('login_desc'); ?></p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <form method="post" action="">
                <div class="field">
                    <label for="username"><?php echo __('login_label'); ?></label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="field">
                    <label for="password"><?php echo __('password'); ?></label>
                    <input type="password" id="password" name="password" required>
                </div>
                <button type="submit" class="btn-primary"><?php echo __('login_btn'); ?></button>
            </form>
            
            <div class="auth-footer">
                <?php echo __('no_account'); ?> <a href="register.php<?php echo isset($_GET['_ijt']) ? '?_ijt=' . $_GET['_ijt'] : ''; ?>"><?php echo __('register_here'); ?></a>
                <br>
                <a href="recovery.php<?php echo isset($_GET['_ijt']) ? '?_ijt=' . $_GET['_ijt'] : ''; ?>"><?php echo __('forgot_password'); ?></a>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
