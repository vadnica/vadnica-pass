<?php include "header.php"; ?>
<?php include "navbar.php"; ?>

<div class="modal" id="modal-add">
    <div class="modal-container">
        <div class="modal-top"><span class="close-icon"></span></div>
        <div class="modal-content">
            <div class="enter-pass">
                <?php $token = isset($_GET['_ijt']) ? "&_ijt=" . $_GET['_ijt'] : ""; ?>
                <form action="enter_data.php?cat=bank<?php echo $token; ?>" method="post" autocomplete="off">
                    <h1><?php echo __('add_new_password_h_bank'); ?></h1>
                    <label for="name"><?php echo __('add_new_password_title'); ?></label>
                    <input type="text" id="name" name="name" placeholder="npr. Nova KBM" required>
                    <label for="user"><?php echo __('add_new_password_username'); ?></label>
                    <input type="text" id="user" name="user" required>
                    <label for="pass"><?php echo __('add_new_password_password'); ?></label>
                    <div class="password-input-container">
                        <input type="password" id="pass" name="pass" required>
                        <?php echo renderPasswordGenerator('pass'); ?>
                    </div>
                    <label for="email"><?php echo __('add_new_password_email'); ?></label>
                    <input type="email" id="email" name="email">
                    <label for="url"><?php echo __('add_new_password_web_adress'); ?></label>
                    <input type="url" id="url" name="url" placeholder="https://...">
                    
                    <div style="border-top: 1px solid #eee; margin-top: 15px; padding-top: 15px; display: flex; flex-direction: column; gap: 16px;">
                        <div class="field-group">
                            <label for="card_number"><?php echo __('card_number'); ?></label>
                            <input type="text" id="card_number" name="card_number" placeholder="xxxx xxxx xxxx xxxx" style="width: 100%;">
                        </div>
                        
                        <div style="display: flex; gap: 10px;">
                            <div style="flex: 1;">
                                <label for="card_pin"><?php echo __('card_pin'); ?></label>
                                <input type="text" id="card_pin" name="card_pin" maxlength="4" placeholder="1234" style="width: 100%;">
                            </div>
                            <div style="flex: 1;">
                                <label for="card_cvv"><?php echo __('card_cvv'); ?></label>
                                <input type="text" id="card_cvv" name="card_cvv" maxlength="3" placeholder="123" style="width: 100%;">
                            </div>
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
        $sql = "SELECT * FROM passwords WHERE category = 'bank' AND user_id = ? AND is_deleted = 0 ORDER BY id DESC";
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
                                <th>' . __('card_number') . '</th>
                                <th>' . __('card_pin') . '</th>
                                <th>CVV</th>
                                <th>' . __('auth_code') . '</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';
            
            foreach ($podatki as $row) {
                renderPasswordRow($row, 'bank');
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