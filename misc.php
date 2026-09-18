<?php include "header.php"; ?>
<?php include "navbar.php"; ?>

<div class="modal" id="modal-add">
    <div class="modal-container">
        <div class="modal-top"><span class="close-icon"></span></div>
        <div class="modal-content">
            <div class="enter-pass">
                <form action="enter_data.php?cat=misc" method="post" autocomplete="off">
                    <h1><?php echo __('add_new_password_h_other'); ?></h1>
                    <label for="name"><?php echo __('add_new_password_title'); ?></label>
                    <input type="text" id="name" name="name" placeholder="npr. WiFi geslo" required>
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
                    <label for="auth_code"><?php echo __('auth_code'); ?></label>
                    <input type="text" id="auth_code" name="auth_code" placeholder="2FA, backup koda...">
                    <input type="submit" class="btn-primary" value="<?= __('add_new_password_save') ?>">
                </form>
            </div>
        </div>
    </div>
</div>

<?php
    try {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM passwords WHERE category = 'misc' AND user_id = ? AND is_deleted = 0 ORDER BY id DESC";
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
                                <th>' . __('show_card_email') . '</th>
                                <th>' . __('show_card_web_adress') . '</th>
                                <th>' . __('auth_code') . '</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>';
            
            foreach ($podatki as $row) {
                renderPasswordRow($row, 'misc');
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