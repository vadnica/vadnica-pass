<?php
session_start();
include_once "config.php";
include_once "languages.php";
include_once "functions.php";

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);

    if (empty($username) || empty($password) || empty($email)) {
        $error = __('fields_required');
    } elseif ($password !== $confirm_password) {
        $error = __('passwords_dont_match');
    } else {
        try {
            // Preveri če uporabnik že obstaja
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            if ($stmt->fetch()) {
                $error = __('username_taken');
            } else {
                // GENERIRANJE DEK IN RECOVERY CODE
                $raw_dek = random_bytes(32);
                $dek_b64 = base64_encode($raw_dek);
                
                $recovery_code = strtoupper(bin2hex(random_bytes(10))); // 20 znakov
                $recovery_code_b64 = base64_encode($recovery_code);
                
                // KEK iz gesla
                $password_kek_b64 = base64_encode(hash('sha256', $password, true));
                
                $encrypted_dek = encryptthis($dek_b64, $password_kek_b64);
                $recovery_dek = encryptthis($dek_b64, $recovery_code_b64);

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $activation_code = bin2hex(random_bytes(16));
                $totp_secret = !empty($_POST['totp_secret']) ? encryptthis($_POST['totp_secret'], $dek_b64) : null;

                $stmt = $pdo->prepare("INSERT INTO users 
    (username, password, email, activation_code, is_active, totp_secret, encrypted_dek, recovery_dek) VALUES (?, ?, ?, ?, 0, ?, ?, ?)");
                $stmt->execute([$username, $hashed_password, $email, $activation_code, $totp_secret, $encrypted_dek, $recovery_dek]);
                
                $activation_link = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) .
                        "/activate.php?code=" . $activation_code;
                
                // Dodaj _ijt žeton za lokalni strežnik, če obstaja
                if (isset($_GET['_ijt'])) {
                    $activation_link .= "&_ijt=" . $_GET['_ijt'];
                }
                
                // Pošiljanje pravega e-maila
                $mailSent = sendActivationEmail($email, $activation_link);
                
                if ($mailSent) {
                    $success = __('registration_success_recovery');
                } else {
                    $success = __('registration_success_no_mail') . "<a href='$activation_link'>" .
                            __('activate_account') . "</a>";
                }
            }
        } catch (\PDOException $e) {
            $error = __('registration_error') . $e->getMessage();
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
            <p><?php echo __('register_desc'); ?></p>
        </div>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form action="register.php<?php echo isset($_GET['_ijt']) ? '?_ijt=' . $_GET['_ijt'] : ''; ?>" method="post">
            <div class="field">
                <label for="username"><?php echo __('username'); ?></label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="field">
                <label for="email"><?php echo __('email_label'); ?></label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="field">
                <label for="password"><?php echo __('password'); ?></label>
                <div class="password-input-container">
                    <input type="password" id="password" name="password" required>
                    <?php echo renderPasswordGenerator('password'); ?>
                </div>
            </div>
            <div class="field">
                <label for="confirm_password"><?php echo __('confirm_password'); ?></label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>

            <!-- Skrito polje za 2FA secret -->
            <input type="hidden" id="totp_secret" name="totp_secret" value="">

            <button type="submit" class="btn-primary"><?php echo __('register_btn'); ?></button>
            <button type="button" class="btn-primary modal-button" data-target="modal2FA">Omogoči avtentikator</button>
        </form>

        <!-- Modal za Recovery Code -->
        <?php if (!empty($recovery_code)): ?>
        <div id="modalRecovery" class="modal" style="display: block;">
            <div class="modal-container">
                <div class="modal-content">
                    <h1><?php echo __('recovery_code_title'); ?></h1>
                    <p><?php echo __('recovery_code_desc'); ?></p>
                    
                    <div class="recovery-code-value" id="recoveryCodeValue"><?php echo $recovery_code; ?></div>
                    
                    <button type="button" class="btn-primary" onclick="copyRecoveryCode()">
                        <?php echo __('show_card_copy'); ?>
                    </button>
                    
                    <div class="recovery-code-warning" style="margin-top: 20px; font-size: 1rem;">
                        <?php echo __('recovery_modal_text'); ?>
                    </div>
                    
                    <div style="margin-top: 30px; border-top: 1px solid var(--border-color); padding-top: 20px;">
                        <p style="font-weight: bold;"><?php echo __('recovery_modal_confirm_text'); ?></p>
                        <button type="button" class="btn-primary" style="width: 100%;" onclick="closeRecoveryModal()">
                            <?php echo __('recovery_modal_confirm_btn'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Modal za 2FA -->
        <div id="modal2FA" class="modal">
            <div class="modal-container">
                <div class="modal-top">
                    <span class="close-icon">&times;</span>
                </div>
                <div class="modal-content">
                    <h1>Avtentikator</h1>

                    <p>Vnesi ta ključ v aplikacijo na telefonu:</p>

                    <div class="secret-display" id="secretDisplay" style="font-family: monospace; font-size: 1.1em;
                    letter-spacing: 2px; word-break: break-all; padding: 12px; background: #f5f5f5; border-radius: 8px;
                    margin-bottom: 16px;">-</div>
                    <button type="button" id="copyBtn" onclick="copySecret()" class="btn-primary">Kopiraj</button>
                    <br><br>
                    <p>Vnesi 6-mestno kodo iz aplikacije:</p>

                    <label for="codeInput"></label>

                    <input type="text" id="codeInput" maxlength="6" placeholder="000000" style="width: 100%;
                    padding: 10px 14px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 1rem;
                    text-align: center; letter-spacing: 4px; box-sizing: border-box;">

                    <div class="result" id="verifyResult" style="margin-top:12px;min-height:20px;"></div>

                    <button type="button" class="btn-primary" style="margin-top:16px;width:100%;" onclick="confirm2FA()">
                        Potrdi
                    </button>
                </div>
            </div>
        </div>
        
        <div class="auth-footer">
            <?php echo __('already_have_account'); ?> <a href="login.php<?php echo isset($_GET['_ijt']) ? '?_ijt=' . $_GET['_ijt'] : ''; ?>"><?php echo __('login_here'); ?></a>
        </div>
        </div>
    </div>
</div>

<script src="authenticator.js"></script>

<?php include "footer.php"; ?>
