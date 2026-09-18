<?php
include "header.php";
include "navbar.php";

if (!isset($_GET['id'])) {
    $token = isset($_GET['_ijt']) ? "?_ijt=" . $_GET['_ijt'] : "";
    header("Location: index.php" . $token);
    exit;
}

$id = $_GET['id'];
$category = $_GET['cat'] ?? 'misc';
$user_id = $_SESSION['user_id'];

// Pridobivanje trenutnih podatkov
try {
    $sql = "SELECT * FROM passwords WHERE id = ? AND user_id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id, $user_id]);
    $row = $stmt->fetch();

    if (!$row) {
        $token = isset($_GET['_ijt']) ? "?_ijt=" . $_GET['_ijt'] : "";
        header("Location: index.php" . $token);
        exit;
    }

    $name = $row['name'];
    $user = decryptthis($row['user']);
    $pass = decryptthis($row['pass']);
    $email = decryptthis($row['email']);
    $url = $row['url'];
    $card_number = !empty($row['card_number']) ? decryptthis($row['card_number']) : '';
    $card_pin = !empty($row['card_pin']) ? decryptthis($row['card_pin']) : '';
    $card_cvv = !empty($row['card_cvv']) ? decryptthis($row['card_cvv']) : '';
    $wifi_5g = !empty($row['wifi_5g']) ? decryptthis($row['wifi_5g']) : '';
    $wifi_2_4g = !empty($row['wifi_2_4g']) ? decryptthis($row['wifi_2_4g']) : '';
    $router_ip = !empty($row['router_ip']) ? decryptthis($row['router_ip']) : '';
    $auth_code = !empty($row['auth_code']) ? decryptthis($row['auth_code']) : '';

} catch (\PDOException $e) {
    die("Napaka: " . $e->getMessage());
}

// Obdelava posodobitve
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_name = $_POST['name'] ?? $name;
    $new_user = $_POST['user'] ?? $user;
    $new_pass = $_POST['pass'] ?? $pass;
    $new_email = $_POST['email'] ?? $email;
    $new_url = $_POST['url'] ?? $url;
    $new_card_number = $_POST['card_number'] ?? $card_number;
    $new_card_pin = $_POST['card_pin'] ?? $card_pin;
    $new_card_cvv = $_POST['card_cvv'] ?? $card_cvv;
    $new_wifi_5g = $_POST['wifi_5g'] ?? $wifi_5g;
    $new_wifi_2_4g = $_POST['wifi_2_4g'] ?? $wifi_2_4g;
    $new_router_ip = $_POST['router_ip'] ?? $router_ip;
    $new_auth_code = $_POST['auth_code'] ?? $auth_code;

    $userencrypted = encryptthis($new_user);
    $passencrypted = encryptthis($new_pass);
    $mailencrypted = encryptthis($new_email);
    $cardnum_encrypted = !empty($new_card_number) ? encryptthis($new_card_number) : null;
    $cardpin_encrypted = !empty($new_card_pin) ? encryptthis($new_card_pin) : null;
    $cardcvv_encrypted = !empty($new_card_cvv) ? encryptthis($new_card_cvv) : null;
    $wifi5_encrypted = !empty($new_wifi_5g) ? encryptthis($new_wifi_5g) : null;
    $wifi24_encrypted = !empty($new_wifi_2_4g) ? encryptthis($new_wifi_2_4g) : null;
    $routerip_encrypted = !empty($new_router_ip) ? encryptthis($new_router_ip) : null;
    $auth_encrypted = !empty($new_auth_code) ? encryptthis($new_auth_code) : null;

    try {
        $sql = "UPDATE passwords SET name = ?, user = ?, pass = ?, email = ?, url = ?, card_number = ?, card_pin = ?, card_cvv = ?, wifi_5g = ?, wifi_2_4g = ?, router_ip = ?, auth_code = ? WHERE id = ? AND user_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$new_name, $userencrypted, $passencrypted, $mailencrypted, $new_url, $cardnum_encrypted, $cardpin_encrypted, $cardcvv_encrypted, $wifi5_encrypted, $wifi24_encrypted, $routerip_encrypted, $auth_encrypted, $id, $user_id]);

        $redirect = "index.php?cat=" . $category;
        $token = isset($_GET['_ijt']) ? (strpos($redirect, '?') !== false ? "&" : "?") . "_ijt=" . $_GET['_ijt'] : "";
        header("Location: " . $redirect . $token);
        exit;
    } catch (\PDOException $e) {
        $error = "Napaka pri posodabljanju: " . $e->getMessage();
    }
}
?>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h1><?php echo __('edit_password'); ?></h1>
            <p><?php echo htmlspecialchars($name); ?></p>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="enter-pass">
            <form method="post" autocomplete="off">
                <label for="name"><?php echo __('add_new_password_title'); ?></label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
                
                <label for="user"><?php echo __('add_new_password_username'); ?></label>
                <input type="text" id="user" name="user" value="<?php echo htmlspecialchars($user); ?>" required>
                
                <label for="pass"><?php echo __('add_new_password_password'); ?></label>
                <div class="password-input-container">
                    <input type="text" id="pass" name="pass" value="<?php echo htmlspecialchars($pass); ?>" required>
                    <?php echo renderPasswordGenerator('pass'); ?>
                </div>
                
                <label for="email"><?php echo __('add_new_password_email'); ?></label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                
                <label for="url"><?php echo __('add_new_password_web_adress'); ?></label>
                <input type="url" id="url" name="url" value="<?php echo htmlspecialchars($url); ?>" placeholder="https://...">
                
                <?php if ($category == 'bank'): ?>
                <div style="border-top: 1px solid #eee; margin-top: 15px; padding-top: 15px; display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label for="card_number"><?php echo __('card_number'); ?></label>
                        <input type="text" id="card_number" name="card_number" value="<?php echo htmlspecialchars($card_number); ?>" placeholder="xxxx xxxx xxxx xxxx" style="width: 100%;">
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <div style="flex: 1;">
                            <label for="card_pin"><?php echo __('card_pin'); ?></label>
                            <div class="password-input-container">
                                <input type="text" id="card_pin" name="card_pin" value="<?php echo htmlspecialchars($card_pin); ?>" maxlength="4" placeholder="1234" style="width: 100%;">
                                <?php echo renderPasswordGenerator('card_pin'); ?>
                            </div>
                        </div>
                        <div style="flex: 1;">
                            <label for="card_cvv"><?php echo __('card_cvv'); ?></label>
                            <div class="password-input-container">
                                <input type="text" id="card_cvv" name="card_cvv" value="<?php echo htmlspecialchars($card_cvv); ?>" maxlength="3" placeholder="123" style="width: 100%;">
                                <?php echo renderPasswordGenerator('card_cvv'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <?php if ($category == 'network'): ?>
                <div style="border-top: 1px solid #eee; margin-top: 15px; padding-top: 15px; display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label for="router_ip"><?php echo __('router_ip'); ?></label>
                        <input type="text" id="router_ip" name="router_ip" value="<?php echo htmlspecialchars($router_ip); ?>" placeholder="192.168.0.1" style="width: 100%;">
                    </div>
                    <div>
                        <label for="wifi_5g"><?php echo __('wifi_5g'); ?></label>
                        <div class="password-input-container">
                            <input type="text" id="wifi_5g" name="wifi_5g" value="<?php echo htmlspecialchars($wifi_5g); ?>" style="width: 100%;">
                            <?php echo renderPasswordGenerator('wifi_5g'); ?>
                        </div>
                    </div>
                    <div>
                        <label for="wifi_2_4g"><?php echo __('wifi_2_4g'); ?></label>
                        <div class="password-input-container">
                            <input type="text" id="wifi_2_4g" name="wifi_2_4g" value="<?php echo htmlspecialchars($wifi_2_4g); ?>" style="width: 100%;">
                            <?php echo renderPasswordGenerator('wifi_2_4g'); ?>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <div style="border-top: 1px solid #eee; margin-top: 15px; padding-top: 15px; display: flex; flex-direction: column; gap: 16px;">
                    <div>
                        <label for="auth_code"><?php echo __('auth_code'); ?></label>
                        <input type="text" id="auth_code" name="auth_code" value="<?php echo htmlspecialchars($auth_code); ?>" placeholder="2FA, backup koda..." style="width: 100%;">
                    </div>
                </div>
                
                <div style="margin-top: 20px; display: flex; gap: 10px;">
                    <input type="submit" class="btn-primary" style="flex: 1;" value="<?php echo __('add_new_password_save'); ?>">
                    <a href="<?php 
                        $back = "index.php?cat=" . $category;
                        $token = isset($_GET['_ijt']) ? "&_ijt=" . $_GET['_ijt'] : "";
                        echo $back . $token; 
                    ?>" class="btn-logout" style="flex: 1; background-color: #f3f4f6; color: var(--text-main); line-height: 2.5; display: flex; align-items: center; justify-content: center;"><?php echo __('two_fa_cancel'); ?></a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
