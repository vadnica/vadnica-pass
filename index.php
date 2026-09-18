<?php include "header.php"; ?>
<?php include "navbar.php"; ?>

<?php
$user_id = $_SESSION['user_id'];
$selected_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$category_filter = isset($_GET['cat']) ? $_GET['cat'] : null;

try {
    $sql = "SELECT * FROM passwords WHERE user_id = ? AND is_deleted = 0";
    $params = [$user_id];
    
    if ($category_filter) {
        $sql .= " AND category = ?";
        $params[] = $category_filter;
    }
    
    $sql .= " ORDER BY name ASC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $all_passwords = $stmt->fetchAll();
    
    $selected_password = null;
    if ($selected_id) {
        foreach ($all_passwords as $p) {
            if ($p['id'] == $selected_id) {
                $selected_password = $p;
                break;
            }
        }
    }
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>

<section class="password-list-panel">
    <div class="password-list-header">
        <h2><?php echo $category_filter ? __($category_filter) : __('all_passwords'); ?></h2>
        <button class="btn-primary modal-button" data-target="modal-add" style="padding: 5px 10px; font-size: 12px;">➕</button>
    </div>
    <div class="password-list-items">
        <?php if (count($all_passwords) > 0): ?>
            <?php foreach ($all_passwords as $row): ?>
                <a href="?id=<?php echo $row['id']; ?><?php echo $category_filter ? '&cat='.$category_filter : ''; ?>" style="text-decoration: none; color: inherit;">
                    <?php renderPasswordListItem($row, $selected_id == $row['id']); ?>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state" style="padding: 20px; text-align: center; color: var(--text-muted);">
                <?php echo __('add_new_password_empty_state'); ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="password-details-panel">
    <?php if ($selected_password): ?>
        <div style="padding: 20px;">
            <?php renderPasswordCard($selected_password, $selected_password['category']); ?>
        </div>
    <?php else: ?>
        <div class="details-placeholder">
            <div class="icon">🛡️</div>
            <h3><?php echo __('select_password_to_view'); ?></h3>
            <p><?php echo __('select_password_desc'); ?></p>
        </div>
    <?php endif; ?>
</section>

<!-- Modal for adding new password -->
<div class="modal" id="modal-add">
    <div class="modal-container">
        <div class="modal-top"><span class="close-icon"></span></div>
        <div class="modal-content">
            <div class="enter-pass">
                <form action="enter_data.php" method="post" autocomplete="off">
                    <h1><?php echo __('add_new_password'); ?></h1>
                    
                    <label for="category"><?php echo __('category'); ?></label>
                    <select name="cat" id="category-select" required style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid var(--border-color); background: var(--input-bg); color: var(--text-main); margin-bottom: 15px;">
                        <option value="bank" <?php echo $category_filter == 'bank' ? 'selected' : ''; ?>><?php echo __('bank'); ?></option>
                        <option value="email" <?php echo $category_filter == 'email' ? 'selected' : ''; ?>><?php echo __('email'); ?></option>
                        <option value="net" <?php echo $category_filter == 'net' ? 'selected' : ''; ?>><?php echo __('internet'); ?></option>
                        <option value="store" <?php echo $category_filter == 'store' ? 'selected' : ''; ?>><?php echo __('stores'); ?></option>
                        <option value="game" <?php echo $category_filter == 'game' ? 'selected' : ''; ?>><?php echo __('game'); ?></option>
                        <option value="network" <?php echo $category_filter == 'network' ? 'selected' : ''; ?>><?php echo __('network'); ?></option>
                        <option value="desktop" <?php echo $category_filter == 'desktop' ? 'selected' : ''; ?>><?php echo __('desktop'); ?></option>
                        <option value="misc" <?php echo $category_filter == 'misc' ? 'selected' : ''; ?>><?php echo __('misc'); ?></option>
                    </select>

                    <label for="name"><?php echo __('add_new_password_title'); ?></label>
                    <input type="text" id="name" name="name" required>
                    
                    <div id="standard-fields">
                        <label for="user"><?php echo __('add_new_password_username'); ?></label>
                        <input type="text" id="user" name="user">
                        
                        <label for="pass"><?php echo __('add_new_password_password'); ?></label>
                        <div class="password-input-container">
                            <input type="password" id="pass" name="pass">
                            <?php echo renderPasswordGenerator('pass_add'); ?>
                        </div>
                    </div>

                    <div id="bank-fields" style="display: none; border-top: 1px solid var(--border-color); margin-top: 10px; padding-top: 10px;">
                        <label for="card_number"><?php echo __('card_number'); ?></label>
                        <input type="text" id="card_number" name="card_number" placeholder="xxxx xxxx xxxx xxxx">
                        <div style="display: flex; gap: 10px;">
                            <div style="flex: 1;">
                                <label for="card_pin"><?php echo __('card_pin'); ?></label>
                                <input type="text" id="card_pin" name="card_pin" maxlength="4" placeholder="1234">
                            </div>
                            <div style="flex: 1;">
                                <label for="card_cvv">CVV</label>
                                <input type="text" id="card_cvv" name="card_cvv" maxlength="3" placeholder="123">
                            </div>
                        </div>
                    </div>

                    <div id="network-fields" style="display: none; border-top: 1px solid var(--border-color); margin-top: 10px; padding-top: 10px;">
                        <label for="wifi_5g"><?php echo __('wifi_5g'); ?></label>
                        <div class="password-input-container">
                            <input type="password" id="wifi_5g" name="wifi_5g">
                            <?php echo renderPasswordGenerator('wifi_5g_add'); ?>
                        </div>
                        <label for="wifi_2_4g"><?php echo __('wifi_2_4g'); ?></label>
                        <div class="password-input-container">
                            <input type="password" id="wifi_2_4g" name="wifi_2_4g">
                            <?php echo renderPasswordGenerator('wifi_2_4g_add'); ?>
                        </div>
                        <label for="router_ip"><?php echo __('router_ip'); ?></label>
                        <input type="text" id="router_ip" name="router_ip" placeholder="192.168.0.1">
                    </div>

                    <div id="extra-fields" style="margin-top: 10px;">
                        <label for="email"><?php echo __('add_new_password_email'); ?></label>
                        <input type="email" id="email" name="email">
                        
                        <label for="url"><?php echo __('add_new_password_web_adress'); ?></label>
                        <input type="url" id="url" name="url" placeholder="https://...">

                        <label for="auth_code"><?php echo __('auth_code'); ?></label>
                        <input type="text" id="auth_code" name="auth_code" placeholder="2FA secret key...">
                    </div>

                    <input type="submit" class="btn-primary" value="<?= __('add_new_password_save') ?>" style="margin-top: 20px; width: 100%;">
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const catSelect = document.getElementById('category-select');
    const bankFields = document.getElementById('bank-fields');
    const networkFields = document.getElementById('network-fields');

    function toggleFields() {
        const val = catSelect.value;
        bankFields.style.display = (val === 'bank') ? 'block' : 'none';
        networkFields.style.display = (val === 'network') ? 'block' : 'none';
    }

    catSelect.addEventListener('change', toggleFields);
    toggleFields(); // Initial call
});
</script>

<?php include "footer.php"; ?>
