<?php include "header.php"; ?>
<?php include "navbar.php"; ?>

<div class="modal" id="modal-add">
    <div class="modal-container">
        <div class="modal-top"><span class="close-icon"></span></div>
        <div class="modal-content">
            <div class="enter-pass">
                <?php $token = isset($_GET['_ijt']) ? "&_ijt=" . $_GET['_ijt'] : ""; ?>
                <form action="enter_data.php?cat=network<?php echo $token; ?>" method="post" autocomplete="off">
                    <h1><?php echo __('add_new_password_h_network'); ?></h1>
                    
                    <label for="name"><?php echo __('add_new_password_title'); ?></label>
                    <input type="text" id="name" name="name" placeholder="npr. Domači usmerjevalnik" required>
                    
                    <div style="border-bottom: 1px solid #eee; margin-bottom: 15px; padding-bottom: 15px;">
                        <h3 style="margin-bottom: 10px;">Router Login</h3>
                        <label for="user"><?php echo __('add_new_password_username'); ?></label>
                        <input type="text" id="user" name="user" required>
                        <label for="pass"><?php echo __('add_new_password_password'); ?></label>
                        <div class="password-input-container">
                            <input type="password" id="pass" name="pass" required>
                            <?php echo renderPasswordGenerator('pass'); ?>
                        </div>
                        <label for="router_ip"><?php echo __('router_ip'); ?></label>
                        <input type="text" id="router_ip" name="router_ip" placeholder="192.168.0.1">
                    </div>

                    <div style="margin-bottom: 15px;">
                        <h3 style="margin-bottom: 10px;">WiFi Settings</h3>
                        <label for="wifi_5g"><?php echo __('wifi_5g'); ?></label>
                        <div class="password-input-container">
                            <input type="password" id="wifi_5g" name="wifi_5g">
                            <?php echo renderPasswordGenerator('wifi_5g'); ?>
                        </div>
                        <label for="wifi_2_4g"><?php echo __('wifi_2_4g'); ?></label>
                        <div class="password-input-container">
                            <input type="password" id="wifi_2_4g" name="wifi_2_4g">
                            <?php echo renderPasswordGenerator('wifi_2_4g'); ?>
                        </div>
                    </div>

                    <div style="border-top: 1px solid #eee; margin-top: 15px; padding-top: 15px;">
                        <label for="auth_code"><?php echo __('auth_code'); ?></label>
                        <input type="text" id="auth_code" name="auth_code" placeholder="2FA, backup koda..." style="width: 100%;">
                    </div>
                    
                    <input type="submit" class="btn-primary" value="<?= __('add_new_password_save') ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<?php
    try {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM passwords WHERE category = 'network' AND user_id = ? AND is_deleted = 0 ORDER BY id DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$user_id]);
        $podatki = $stmt->fetchAll();

        if (count($podatki) > 0) {
            echo '<div class="password-table-container">
                    <table class="password-table">
                        <thead>
                            <tr>
                                <th></th>
                                <th>' . __('add_new_password_title') . '</th>
                                <th>' . __('show_card_username') . '</th>
                                <th>' . __('show_card_password') . '</th>
                                <th>' . __('wifi_5g') . '</th>
                                <th>' . __('wifi_2_4g') . '</th>
                                <th>' . __('router_ip') . '</th>
                                <th>' . __('auth_code') . '</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';
            
            foreach ($podatki as $row) {
                renderPasswordRow($row, 'network');
            }
            
            echo '      </tbody>
                    </table>
                  </div>';
        } else {
            echo "<div class='empty-state'>" . __('add_new_password_empty_state') . "</div>";
        }
    } catch (\PDOException $e) {
        echo "<div class='alert alert-danger'>" . __('add_new_password_alert_danger') . $e->getMessage() . "</div>";
    }
    ?>

<?php include "footer.php"; ?>
