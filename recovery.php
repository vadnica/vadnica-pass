<?php
ob_start();
session_start();
include_once "config.php";
include_once "languages.php";
include_once "functions.php";

$error = '';
$success = '';
$step = 1; // 1: Vnos recovery code, 2: Vnos novega gesla

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['recovery_code'])) {
        $username = trim($_POST['username'] ?? '');
        $recovery_code = trim($_POST['recovery_code'] ?? '');

        try {
            $stmt = $pdo->prepare("SELECT id, recovery_dek FROM users WHERE username = ? OR email = ? LIMIT 1");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && !empty($user['recovery_dek'])) {
                $recovery_code_b64 = base64_encode($recovery_code);
                $decrypted_dek = decryptthis($user['recovery_dek'], $recovery_code_b64);

                if ($decrypted_dek !== false) {
                    $_SESSION['recovery_user_id'] = $user['id'];
                    $_SESSION['recovery_dek'] = $decrypted_dek;
                    $step = 2;
                } else {
                    $error = $_SESSION['lang'] == 'sl' ? "Napačna obnovitvena koda." : "Invalid recovery code.";
                }
            } else {
                $error = $_SESSION['lang'] == 'sl' ? "Uporabnik ne obstaja ali nima nastavljene obnovitvene kode." : "User not found or recovery code not set.";
            }
        } catch (\PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    } elseif (isset($_POST['new_password'])) {
        $new_pass = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'];
        $user_id = $_SESSION['recovery_user_id'] ?? null;
        $dek_b64 = $_SESSION['recovery_dek'] ?? null;

        if (!$user_id || !$dek_b64) {
            header("Location: recovery.php");
            exit;
        }

        if ($new_pass !== $confirm_pass) {
            $error = __('passwords_dont_match');
            $step = 2;
        } else {
            try {
                // Ponovno šifriranje DEK z novim geslom
                $password_kek_b64 = base64_encode(hash('sha256', $new_pass, true));
                $new_encrypted_dek = encryptthis($dek_b64, $password_kek_b64);
                
                $new_hashed_password = password_hash($new_pass, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare("UPDATE users SET password = ?, encrypted_dek = ? WHERE id = ?");
                $stmt->execute([$new_hashed_password, $new_encrypted_dek, $user_id]);

                unset($_SESSION['recovery_user_id']);
                unset($_SESSION['recovery_dek']);
                $success = $_SESSION['lang'] == 'sl' ? "Geslo je bilo uspešno ponastavljeno. Zdaj se lahko prijavite." : "Password reset successful. You can now log in.";
                $step = 3; // Success
            } catch (\PDOException $e) {
                $error = "Error: " . $e->getMessage();
                $step = 2;
            }
        }
    }
}

include "header.php";
include "navbar.php";
?>

<div class="auth-container auth-page">
    <div class="auth-stack">
        <div class="auth-card">
        <div class="auth-header">
            <h1><?php echo $_SESSION['lang'] == 'sl' ? 'Obnova računa' : 'Account Recovery'; ?></h1>
            <p><?php echo $_SESSION['lang'] == 'sl' ? 'Ponastavite geslo s pomočjo obnovitvene kode.' : 'Reset your password using your recovery code.'; ?></p>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <div class="auth-footer">
                <a href="login.php<?php echo isset($_GET['_ijt']) ? '?_ijt=' . $_GET['_ijt'] : ''; ?>" class="btn-primary" style="display: block; text-decoration: none; text-align: center;"><?php echo __('login'); ?></a>
            </div>
        <?php elseif ($step == 1): ?>
            <form method="post" action="">
                <div class="field">
                    <label for="username"><?php echo __('login_label'); ?></label>
                    <input type="text" id="username" name="username" required>
                </div>
                <div class="field">
                    <label for="recovery_code"><?php echo __('recovery_code_title'); ?></label>
                    <input type="text" id="recovery_code" name="recovery_code" placeholder="A1B2C3D4..." required>
                </div>
                <button type="submit" class="btn-primary"><?php echo $_SESSION['lang'] == 'sl' ? 'Preveri kodo' : 'Verify Code'; ?></button>
            </form>
        <?php elseif ($step == 2): ?>
            <form method="post" action="">
                <div class="field">
                    <label for="new_password"><?php echo $_SESSION['lang'] == 'sl' ? 'Novo geslo' : 'New Password'; ?></label>
                    <input type="password" id="new_password" name="new_password" required>
                </div>
                <div class="field">
                    <label for="confirm_password"><?php echo __('confirm_password'); ?></label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn-primary"><?php echo $_SESSION['lang'] == 'sl' ? 'Ponastavi geslo' : 'Reset Password'; ?></button>
            </form>
        <?php endif; ?>

        <?php if ($step != 3): ?>
        <div class="auth-footer">
            <a href="login.php<?php echo isset($_GET['_ijt']) ? '?_ijt=' . $_GET['_ijt'] : ''; ?>"><?php echo __('two_fa_cancel'); ?></a>
        </div>
        <?php endif; ?>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
